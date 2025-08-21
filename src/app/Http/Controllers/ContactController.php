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
        $categories = \App\Models\Category::orderBy('id')->get();
        return view('contact', compact('categories'));
    }

    // 確認画面
    public function confirm(ContactRequest $request)
    {
        // バリデーション通過データ
        $input = $request->validated();

        // 電話番号を結合
         $tel = preg_replace('/\D/', '', ($input['tel1'].$input['tel2'].$input['tel3']));
        $input['tel'] = $tel;

        // もし「結合後10〜11桁」も強制したい場合はここで追加チェック
        if (strlen($tel) < 10 || strlen($tel) > 11) {
            return back()
                ->withErrors(['tel' => '電話番号は10〜11桁の数字で入力してください'])
                ->withInput();
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
        $tel = preg_replace('/\D/', '', ($data['tel1'].$data['tel2'].$data['tel3']));
        $data['tel'] = $tel;
        unset($data['tel1'], $data['tel2'], $data['tel3']);

        // ここで10〜11桁チェックをもう一度してもOK
        if (strlen($tel) < 10 || strlen($tel) > 11) {
            return back()
                ->withErrors(['tel' => '電話番号は10〜11桁の数字で入力してください'])
                ->withInput();
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
