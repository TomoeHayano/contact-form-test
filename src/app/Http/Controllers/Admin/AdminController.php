<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexRequest;
use App\Models\Contact;
use App\Models\Category;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;


class AdminController extends Controller
{
   public function index(Request $request)
{
    $q = Contact::query()->with('category');

    // 1) 名前：姓/名/フルネーム + 部分一致/完全一致（デフォ部分一致）
    $name = trim((string)$request->query('name'));
    $nameMatch = $request->query('name_match', 'partial'); // partial|exact
    if ($name !== '') {
        $match = $nameMatch === 'exact' ? '=' : 'LIKE';
        $kw    = $match === '=' ? $name : "%{$name}%";

        $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
        $q->where(function($qq) use ($match,$kw,$parts,$name){
            $qq->where('last_name',  $match, $kw)
               ->orWhere('first_name',$match, $kw);
            if (count($parts) >= 2) {
                [$last,$first] = [$parts[0], $parts[1]];
                $qq->orWhere(function($w) use ($last,$first){
                    $w->where('last_name','LIKE',"%{$last}%")
                      ->where('first_name','LIKE',"%{$first}%");
                });
            }
        });
    }

    // 2) メール
    $email = trim((string)$request->query('email'));
    $emailMatch = $request->query('email_match','partial'); // partial|exact
    if ($email !== '') {
        $match = $emailMatch === 'exact' ? '=' : 'LIKE';
        $kw    = $match === '=' ? $email : "%{$email}%";
        $q->where('email', $match, $kw);
    }

    // 3) 性別（''=未選択, all=全て, 1/2/3）
    $gender = $request->query('gender');
    if ($gender !== null && $gender !== '' && $gender !== 'all') {
        $q->where('gender', (int)$gender);
    }

    // 4) 種別
    if ($request->filled('category_id')) {
        $q->where('category_id', (int)$request->query('category_id'));
    }

    // 5) 日付
    if ($request->filled('date')) {
        $q->whereDate('created_at', $request->query('date'));
    }

    $contacts   = $q->orderByDesc('created_at')->paginate(7)->withQueryString();
    $categories = Category::orderBy('id')->get();

    return view('index', [
        'contacts'=>$contacts,
        'categories'=>$categories,
        'f'=>$request->only(['name','name_match','email','email_match','gender','category_id','date']),
    ]);
}
    public function export(Request $request): StreamedResponse
{
    // index() と同じフィルタを適用（最小：必要分だけコピペ）
    $q = \App\Models\Contact::query()->with('category');

    if (($name = trim((string)$request->query('name'))) !== '') {
        $match = $request->query('name_match','partial') === 'exact' ? '=' : 'LIKE';
        $kw    = $match==='=' ? $name : "%{$name}%";
        $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
        $q->where(function($qq) use ($match,$kw,$parts){
            $qq->where('last_name',$match,$kw)
               ->orWhere('first_name',$match,$kw);
            if (count($parts) >= 2) {
                [$last,$first] = [$parts[0], $parts[1]];
                $qq->orWhere(function($w) use ($last,$first){
                    $w->where('last_name','LIKE',"%{$last}%")
                      ->where('first_name','LIKE',"%{$first}%");
                });
            }
        });
    }
    if (($email = trim((string)$request->query('email'))) !== '') {
        $match = $request->query('email_match','partial') === 'exact' ? '=' : 'LIKE';
        $kw    = $match==='=' ? $email : "%{$email}%";
        $q->where('email',$match,$kw);
    }
    if (($g = $request->query('gender')) !== null && $g !== '' && $g !== 'all') {
        $q->where('gender', (int)$g);
    }
    if ($request->filled('category_id')) {
        $q->where('category_id', (int)$request->query('category_id'));
    }
    if ($request->filled('date')) {
        $q->whereDate('created_at', $request->query('date'));
    }

    $q->orderByDesc('created_at');

    $filename = 'contacts_'.now()->format('Ymd_His').'.csv';
    $headers  = [
        'Content-Type'        => 'text/csv; charset=UTF-8',
        'Content-Disposition' => "attachment; filename={$filename}",
    ];

    return response()->stream(function() use ($q){
        $out = fopen('php://output', 'w');
        // Excel対策のBOM
        fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($out, ['ID','姓','名','性別','メール','種別','作成日']);
        $q->chunk(500, function($rows) use ($out){
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->id,
                    $r->last_name,
                    $r->first_name,
                    ['','男性','女性','その他'][$r->gender] ?? '',
                    $r->email,
                    optional($r->category)->content,
                    optional($r->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
        });
        fclose($out);
    }, 200, $headers);
}

}
