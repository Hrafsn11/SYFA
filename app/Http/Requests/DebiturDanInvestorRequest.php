<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Enums\BanksEnum;
use App\Models\MasterDebiturDanInvestor;
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
            'kode_perusahaan' => 'required_if:flagging,tidak|min:2|max:4|regex:/^[A-Za-z0-9]+$/|unique:master_debitur_dan_investor,kode_perusahaan',
            'alamat' => 'required|max:500',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ],
            'password_confirmation' => 'required_with:password|min:8|same:password',
            'no_telepon' => 'required|max:20',
            'deposito' => 'required_if:flagging,ya|in:reguler,khusus',
            'nama_ceo' => 'required_if:flagging,tidak|max:255',
            'nama_bank' => 'required|in:' . implode(',', BanksEnum::getConstants()),
            'no_rek' => 'required|max:100',
            'npwp' => 'nullable|numeric|unique:master_debitur_dan_investor,npwp',
            'flagging' => 'required|in:ya,tidak',
            'flagging_investor' => [
                'required_if:flagging,ya',
                'nullable',
                'regex:/^(sfinance|sfinlog)(,(sfinance|sfinlog))?$/'
            ],
            'tanda_tangan' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($this->id) {
            $validate['id_kol'] = 'nullable|exists:master_kol,id_kol';
            $validate['kode_perusahaan'] = 'required_if:flagging,tidak|min:2|max:4|regex:/^[A-Za-z0-9]+$/|unique:master_debitur_dan_investor,kode_perusahaan,' . $this->id . ',id_debitur';
            
            $validate['password'] = [
                'nullable',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ];
            $validate['password_confirmation'] = 'required_with:password|min:8|same:password';
            
            $validate['email'] = ['required', 'email', 'max:255', function ($attribute, $value, $fail) {
                $master = MasterDebiturDanInvestor::where('id_debitur', $this->id)->first();
                $user = User::where('email', $value)->where('id', '!=', $master->user_id)->exists();
                if ($user) $fail('Email sudah digunakan.');
            }];

            $validate['tanda_tangan'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
            $validate['npwp'] = 'nullable|numeric|unique:master_debitur_dan_investor,npwp,' . $this->id . ',id_debitur';
        }

        return $validate;
    }

    public function messages()
    {
        return [
            'id_kol.required_if' => 'Kol harus diisi.',
            'id_kol.exists' => 'Kol tidak valid.',
            'nama.required' => 'Nama harus diisi.',
            'kode_perusahaan.required_if' => 'Kode perusahaan harus diisi untuk debitur.',
            'kode_perusahaan.min' => 'Kode perusahaan minimal 2 karakter.',
            'kode_perusahaan.max' => 'Kode perusahaan tidak boleh lebih dari 4 karakter.',
            'kode_perusahaan.regex' => 'Kode perusahaan hanya boleh mengandung huruf dan angka.',
            'kode_perusahaan.unique' => 'Kode perusahaan sudah digunakan.',
            'alamat.required' => 'Alamat harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung minimal satu huruf kapital, satu huruf kecil, dan satu angka.',
            'password_confirmation.required_with' => 'Konfirmasi password harus diisi.',
            'password_confirmation.same' => 'Konfirmasi password tidak cocok.',
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
            'npwp.numeric' => 'NPWP harus berupa angka.',
            'npwp.unique' => 'NPWP sudah terdaftar.',
            'flagging.required' => 'Flagging harus diisi.',
            'flagging.in' => 'Flagging tidak valid.',
            'flagging_investor.required_if' => 'Tipe investor harus diisi.',
            'flagging_investor.regex' => 'Tipe investor tidak valid. Pilih: SFinance, SFinlog, atau Keduanya.',
            'tanda_tangan.required' => 'Tanda tangan harus diisi.',
            'tanda_tangan.image' => 'Tanda tangan harus berupa gambar.',
            'tanda_tangan.mimes' => 'Tanda tangan harus berupa gambar JPEG, PNG, atau JPG.',
            'tanda_tangan.max' => 'Tanda tangan tidak boleh lebih besar dari 2MB.',
        ];
    }
}
