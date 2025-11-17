<?php

namespace App\Http\Requests;

use App\Enums\BanksEnum;
use Illuminate\Foundation\Http\FormRequest;

class DebiturDanInvestorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $validate = [
            'id_kol' => 'required_if:flagging,tidak|exists:master_kol,id_kol',
            'nama' => 'required|max:255',
            'alamat' => 'required_if:flagging,tidak|max:500',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'no_telepon' => 'required|max:20',
            'deposito' => 'required_if:flagging,ya|in:reguler,khusus',
            'nama_ceo' => 'required_if:flagging,tidak|max:255',
            'nama_bank' => 'required|in:' . implode(',', BanksEnum::getConstants()),
            'no_rek' => 'required|max:100',
            'flagging' => 'required|in:ya,tidak',
            'tanda_tangan' => 'required_if:flagging,tidak|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($this->id) {
            $validate['id_kol'] = 'nullable|exists:master_kol,id_kol';
        }

        return $validate;
    }

    public function messages()
    {
        return [
            'id_kol.required_if' => 'Kol harus diisi.',
            'id_kol.exists' => 'Kol tidak valid.',
            'nama.required' => 'Nama harus diisi.',
            'alamat.required_if' => 'Alamat harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Password tidak cocok.',
            'no_telepon.required' => 'Nomor telepon harus diisi.',
            'no_telepon.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
            'deposito.required_if' => 'Deposit harus diisi.',
            'deposito.in' => 'Deposit tidak valid.',
            'nama_ceo.required_if' => 'Nama CEO harus diisi.',
            'nama_ceo.max' => 'Nama CEO tidak boleh lebih dari 255 karakter.',
            'nama_bank.required' => 'Nama bank harus diisi.',
            'nama_bank.in' => 'Nama bank tidak valid.',
            'no_rek.required' => 'Nomor rekening harus diisi.',
            'no_rek.max' => 'Nomor rekening tidak boleh lebih dari 100 karakter.',
            'flagging.required' => 'Flagging harus diisi.',
            'flagging.in' => 'Flagging tidak valid.',
            'tanda_tangan.required_if' => 'Tanda tangan harus diisi.',
            'tanda_tangan.image' => 'Tanda tangan harus berupa gambar.',
            'tanda_tangan.mimes' => 'Tanda tangan harus berupa gambar JPEG, PNG, atau JPG.',
            'tanda_tangan.max' => 'Tanda tangan tidak boleh lebih besar dari 2MB.',
        ];
    }
}
