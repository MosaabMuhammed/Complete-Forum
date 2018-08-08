<?php

namespace App\Http\Requests;

use App\Reply;
use App\Exceptions\TrottleException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', new Reply);
    }

    protected function failedAuthorization()
    {
        throw new TrottleException("You're repling too frequently .. please take a break!");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body'  =>  'required|spamfree'
        ];
    }

    public function persist($thread)
    {

    }
}
