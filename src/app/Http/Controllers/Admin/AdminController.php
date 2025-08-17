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

        // フィルタ入力の取得（可読性の高い名前に統一）
        $nameFilter        = trim((string) $request->query('name'));
        $emailFilter       = trim((string) $request->query('email'));
        $genderFilter      = $request->query('gender');
        $categoryIdFilter  = $request->query('category_id');
        $createdDateFilter = $request->query('date');

        // 1) 名前（姓/名/フルネーム 部分一致）
        if ($nameFilter !== '') {
            $nameParts = preg_split('/\s+/u', $nameFilter, -1, PREG_SPLIT_NO_EMPTY);
            $query->where(function ($subQuery) use ($nameFilter, $nameParts) {
                // 姓 or 名 の部分一致
                $subQuery->where('last_name', 'LIKE', "%{$nameFilter}%")
                         ->orWhere('first_name', 'LIKE', "%{$nameFilter}%");

                // フルネーム（スペース区切りされている場合のみ）
                if (count($nameParts) >= 2) {
                    [$last, $first] = [$nameParts[0], $nameParts[1]];
                    $subQuery->orWhere(function ($fullNameQuery) use ($last, $first) {
                        $fullNameQuery->where('last_name', 'LIKE', "%{$last}%")
                                      ->where('first_name', 'LIKE', "%{$first}%");
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
            $query->where('gender', (int) $genderFilter);
        }

        // 4) 種別（未選択は条件なし）
        if ($categoryIdFilter !== null && $categoryIdFilter !== '') {
            $query->where('category_id', (int) $categoryIdFilter);
        }

        // 5) 日付（created_at の日付一致）
        if ($createdDateFilter !== null && $createdDateFilter !== '') {
            $query->whereDate('created_at', $createdDateFilter);
        }

        // 何も入っていなければ全件（ブランク検索OK）
        $contacts   = $query->orderByDesc('created_at')->paginate(7)->withQueryString();
        $categories = Category::orderBy('id')->get();

        $filters = [
            'name'        => $nameFilter,
            'email'       => $emailFilter,
            'gender'      => (string) ($genderFilter ?? ''),
            'category_id' => (string) ($categoryIdFilter ?? ''),
            'date'        => (string) ($createdDateFilter ?? ''),
        ];

        // 管理画面ビュー（直下 index.blade.php）
        return view('index', compact('contacts', 'categories', 'filters'));
    }

    // 詳細（暗黙の結合: URLの {contact} から自動解決）
    public function show(Contact $contact)
    {
        $contact->load('category');
        return response()->json($contact); // モーダル用
    }

    // CSV エクスポート（教材意図に沿って where ... get() を明示）
    public function export(Request $request): StreamedResponse
    {
        $query = Contact::query()->with('category');

        $nameFilter        = trim((string) $request->query('name'));
        $emailFilter       = trim((string) $request->query('email'));
        $genderFilter      = $request->query('gender');
        $categoryIdFilter  = $request->query('category_id');
        $createdDateFilter = $request->query('date');

        if ($nameFilter !== '') {
            $nameParts = preg_split('/\s+/u', $nameFilter, -1, PREG_SPLIT_NO_EMPTY);
            $query->where(function ($subQuery) use ($nameFilter, $nameParts) {
                $subQuery->where('last_name', 'LIKE', "%{$nameFilter}%")
                         ->orWhere('first_name', 'LIKE', "%{$nameFilter}%");

                if (count($nameParts) >= 2) {
                    [$last, $first] = [$nameParts[0], $nameParts[1]];
                    $subQuery->orWhere(function ($fullNameQuery) use ($last, $first) {
                        $fullNameQuery->where('last_name', 'LIKE', "%{$last}%")
                                      ->where('first_name', 'LIKE', "%{$first}%");
                    });
                }
            });
        }

        if ($emailFilter !== '') {
            $query->where('email', 'LIKE', "%{$emailFilter}%");
        }
        if ($genderFilter !== null && $genderFilter !== '') {
            $query->where('gender', (int) $genderFilter);
        }
        if ($categoryIdFilter !== null && $categoryIdFilter !== '') {
            $query->where('category_id', (int) $categoryIdFilter);
        }
        if ($createdDateFilter !== null && $createdDateFilter !== '') {
            $query->whereDate('created_at', $createdDateFilter);
        }

        $rows = $query->orderByDesc('created_at')->get();

        $filename = 'contacts_' . now()->format('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            // BOM付与（Excel対策）
            fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['ID', '姓', '名', '性別', 'メール', '種別', '作成日']);

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    $row->last_name,
                    $row->first_name,
                    ['','男性','女性','その他'][$row->gender] ?? '',
                    $row->email,
                    optional($row->category)->content,
                    optional($row->created_at)?->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}