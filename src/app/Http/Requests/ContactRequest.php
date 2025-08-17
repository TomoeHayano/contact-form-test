<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
             'last_name'    => ['required','string'],
            'first_name'   => ['required','string'],
            'gender'       => ['required','in:1,2,3'],
            'email'        => ['required','email:filter'],
            'tel'          => ['required','regex:/^\d+$/','max_digits:5'],
            'address'      => ['required','string'],
            'building'     => ['nullable','string'],
            'category_id'  => ['required','exists:categories,id'],
            'detail'       => ['required','string','max:120'],
        
        ];
    }

    public function attributes()
    {
        return [
            'last_name'   => '姓',
            'first_name'  => '名',
            'gender'      => '性別',
            'email'       => 'メールアドレス',
            'tel'         => '電話番号',
            'address'     => '住所',
            'building'    => '建物名',
            'category_id' => 'お問い合わせの種類',
            'detail'      => 'お問い合わせ内容',
        ];
    }

    public function messages()
    {
        return [
            'last_name.required'   => '姓を入力してください',
            'first_name.required'  => '名を入力してください',
            'gender.required'      => '性別を選択してください',
            'gender.in'            => '性別を選択してください',
            'email.required'       => 'メールアドレスを入力してください',
            'email.email'          => 'メールアドレスはメール形式で入力してください',
            'tel.required'         => '電話番号を入力してください',
            'tel.regex'            => '電話番号は半角数字で入力してください',
            'tel.max_digits'       => '電話番号は5桁までの数字で入力してください',
            'address.required'     => '住所を入力してください',
            'category_id.required' => 'お問い合わせの種類を選択してください',
            'category_id.exists'   => 'お問い合わせの種類を選択してください',
            'detail.required'      => 'お問い合わせ内容を入力してください',
            'detail.max'           => 'お問合せ内容は120文字以内で入力してください',
        ];
    }
}
