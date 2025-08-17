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
        $query = Contact::query()->with('category');

        // 入力を取得
        $nameFilter     = trim((string)$request->query('name'));
        $emailFilter    = trim((string)$request->query('email'));
        $genderFilter   = $request->query('gender');
        $categoryFilter = $request->query('category_id');
        $dateFilter     = $request->query('date');

        
        // 1) 名前（姓/名/フルネーム部分一致）
        if ($nameFilter !== '') {
            $parts = preg_split('/\s+/u', $nameFilter, -1, PREG_SPLIT_NO_EMPTY);
            $query->where(function($where) use ($nameFilter, $parts){
                // 姓 or 名 の部分一致
                $where->where('last_name',  'LIKE', "%{$nameFilter}%")
                      ->orWhere('first_name','LIKE', "%{$nameFilter}%");

                // フルネーム（スペース区切りがある場合）
                if (count($parts) >= 2) {
                    [$last,$first] = [$parts[0], $parts[1]];
                    $where->orWhere(function($w) use ($last,$first){
                        $w->where('last_name', 'LIKE', "%{$last}%")
                          ->where('first_name','LIKE', "%{$first}%");
                    });
                }
            });
        }

        // 2) メール（部分一致）
        if ($emailFilter !== '') {
            $query->where('email', 'LIKE', "%{$emailFilter}%");
        }

        // 3) 性別（未選択は条件なし）
        if ($genderFilter !== null && $genderFilter !== '') {
            $query->where('gender', (int)$genderFilter);
        }

        // 4) 種別（未選択は条件なし）
        if ($categoryFilter !== null && $categoryFilter !== '') {
            $query->where('category_id', (int)$categoryFilter);
        }

        // 5) 日付（created_at の日付一致）
        if ($dateFilter !== null && $dateFilter !== '') {
            $query->whereDate('created_at', $dateFilter);
        }

        // 何も入っていなければ全件（= ブランク検索OK）
        $contacts   = $query->orderByDesc('created_at')->paginate(7)->withQueryString();
        $categories = Category::orderBy('id')->get();

        $filters = [
            'name'        => $nameFilter,
            'email'       => $emailFilter,
            'gender'      => (string)($genderFilter ?? ''),
            'category_id' => (string)($categoryFilter ?? ''),
            'date'        => (string)($dateFilter ?? ''),
        ];

        return view('index', compact('contacts','categories','filters'));
    }

    // 詳細（暗黙の結合: URLの {contact} から自動解決）
    public function show(Contact $contact)
    {
        $contact->load('category');
        return response()->json($contact);  // モーダル用
    }

    // CSV エクスポート（教材意図に沿って where ... get() を明示）
    public function export(Request $request): StreamedResponse
    {
        $query = Contact::query()->with('category');

        $nameFilter     = trim((string)$request->query('name'));
        $emailFilter    = trim((string)$request->query('email'));
        $genderFilter   = $request->query('gender');
        $categoryFilter = $request->query('category_id');
        $dateFilter     = $request->query('date');

        if ($nameFilter !== '') {
            $parts = preg_split('/\s+/u', $nameFilter, -1, PREG_SPLIT_NO_EMPTY);
            $query->where(function($where) use ($nameFilter, $parts){
                $where->where('last_name',  'LIKE', "%{$nameFilter}%")
                      ->orWhere('first_name','LIKE', "%{$nameFilter}%");
                if (count($parts) >= 2) {
                    [$last,$first] = [$parts[0], $parts[1]];
                    $where->orWhere(function($w) use ($last,$first){
                        $w->where('last_name','LIKE',"%{$last}%")
                          ->where('first_name','LIKE',"%{$first}%");
                    });
                }
            });
        }
        if ($emailFilter !== '') {
            $query->where('email', 'LIKE', "%{$emailFilter}%");
        }
        if ($genderFilter !== null && $genderFilter !== '') {
            $query->where('gender', (int)$genderFilter);
        }
        if ($categoryFilter !== null && $categoryFilter !== '') {
            $query->where('category_id', (int)$categoryFilter);
        }
        if ($dateFilter !== null && $dateFilter !== '') {
            $query->whereDate('created_at', $dateFilter);
        }

        $rows = $query->orderByDesc('created_at')->get();

        $filename = 'contacts_'.now()->format('Ymd_His').'.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        return response()->stream(function() use ($rows){
            $out = fopen('php://output', 'w');
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
            fputcsv($out, ['ID','姓','名','性別','メール','種別','作成日']);

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
            fclose($out);
        }, 200, $headers);
    }
}