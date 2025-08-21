<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Category;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        // ここだけ変更：view 名を contact に
        $categories = \App\Models\Category::orderBy('id')->get();
        return view('contact', compact('categories'));
    }

    // 確認
    public function confirm(ContactRequest $request)
    {
        // バリデーション通過データ
        $input = $request->validated();

        // 電話番号を結合
        if (isset($contact['tel1']) && isset($contact['tel2']) && isset($contact['tel3'])) {
        $contact['tel'] = $contact['tel1'] . $contact['tel2'] . $contact['tel3'];
        unset($contact['tel1'], $contact['tel2'], $contact['tel3']);
    }

        // 表示用にカテゴリ名を取得
        $categoryName = Category::find($input['category_id'])?->content ?? '';

        return view('confirm', [
            'input'        => $input,
            'categoryName' => $categoryName,
        ]);
    }

    // 保存 → サンクス
    public function store(ContactRequest $request)
    {
        $data = $request->validated();

        // 電話番号を結合
        if (isset($data['tel1']) && isset($data['tel2']) && isset($data['tel3'])) {
        $data['tel'] = $data['tel1'] . $data['tel2'] . $data['tel3'];
        unset($data['tel1'], $data['tel2'], $data['tel3']);
    }

        Contact::create($data);

        return redirect('/thanks');
    }

    // サンクス
    public function thanks()
    {
        return view('thanks');
    }
}
