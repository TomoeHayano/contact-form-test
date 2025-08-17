<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexRequest;
use App\Models\Contact;
use App\Models\Category;
use Illuminate\Http\Request;


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

}
