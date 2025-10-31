<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropAllTables();

        // migration table
        Schema::create('migrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('migration');
            $table->integer('batch');
        });

        // users table
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();

            $table->rememberToken();
            $table->ulid('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });

        // personal_access_tokens table
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // teams table
        Schema::create('teams', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('user_id')->index();
            $table->string('name');
            $table->boolean('personal_team');
            $table->timestamps();
        });

        // team user table
        Schema::create('team_user', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->ulid('team_id');
            $table->ulid('user_id');

            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });

        // team invitations table
        Schema::create('team_invitations', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->ulid('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();

            $table->string('email');
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'email']);
        });

        // permission tables
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        throw_if(empty($tableNames), new Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.'));
        throw_if($teams && empty($columnNames['team_foreign_key'] ?? null), new Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.'));

        Schema::create($tableNames['permissions'], static function (Blueprint $table) {
            // $table->engine('InnoDB');
            $table->ulid('id')->primary()->unique(); // permission id
            $table->string('name');       // For MyISAM use string('name', 225); // (or 166 for InnoDB with Redundant/Compact row format)
            $table->string('guard_name'); // For MyISAM use string('guard_name', 25);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
            // $table->engine('InnoDB');
            $table->ulid('id')->primary()->unique(); // role id
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name');       // For MyISAM use string('name', 225); // (or 166 for InnoDB with Redundant/Compact row format)
            $table->string('guard_name'); // For MyISAM use string('guard_name', 25);
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
            $table->ulid($pivotPermission);

            $table->string('model_type');
            $table->ulid($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            } else {
                $table->primary(
                    [$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            }
        });

        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
            $table->ulid($pivotRole);

            $table->string('model_type');
            $table->ulid($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            } else {
                $table->primary(
                    [$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            }
        });

        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            $table->ulid($pivotPermission);
            $table->ulid($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        try {
            app('cache')
                ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
                ->forget(config('permission.cache.key'));
        } catch (\Exception $e) {
            // Cache table might not exist yet during fresh migrations
            // This is safe to ignore as the cache will be cleared when needed
        }

        // password_reset_tokens table

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // sessions table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->ulid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // master kol table
        Schema::create('master_kol', function (Blueprint $table) {
            $table->ulid('id_kol')->primary()->unique();
            $table->integer('kol');
            $table->decimal('persentase_pencairan', 5, 2)->default(0);
            $table->integer('jmlh_hari_keterlambatan')->default(0);
            $table->timestamps();
        });

        // master_sumber_pendanaan_eksternal table
        Schema::create('master_sumber_pendanaan_eksternal', function (Blueprint $table) {
            $table->ulid('id_instansi')->primary()->unique();
            $table->string('nama_instansi', 255);
            $table->integer('persentase_bagi_hasil')->default(0);
            $table->timestamps();
        });

        // master_debitur_dan_investor table
        Schema::create('master_debitur_dan_investor', function (Blueprint $table) {
            $table->ulid('id_debitur')->primary()->unique();

            $table->ulid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->ulid('id_kol')->nullable();

            $table->string('nama', 255);
            $table->enum('flagging', ['ya', 'tidak'])->default('tidak');
            $table->string('tanda_tangan', 255)->nullable();

            $table->string('alamat', 255)->nullable();
            $table->string('email', 255)->nullable();

            $table->string('no_telepon', 20)->nullable();
            $table->enum('status', ['active', 'non active'])->default('active');
            $table->enum('deposito', ['reguler', 'khusus'])->nullable();

            $table->string('nama_ceo', 255)->nullable();
            $table->enum('nama_bank', ['BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'])->nullable();
            $table->string('no_rek', 100)->nullable();

            $table->timestamps();

            $table->foreign('id_kol')
                  ->references('id_kol')->on('master_kol')
                  ->onDelete('restrict')->onUpdate('cascade');

        });

        // pengembalian pinjaman table
        Schema::create('pengembalian_pinjamen', function (Blueprint $table) {
            $table->ulid()->primary()->unique();
            $table->timestamps();
        });

        // config_matrix_pinjaman table
        Schema::create('config_matrix_pinjaman', function (Blueprint $table) {
            $table->ulid('id_matrix_pinjaman')->primary()->unique();
            $table->decimal('nominal', 15, 2);
            $table->string('approve_oleh', 255)->nullable();
            $table->timestamps();
        });

        // peminjaman_invoice_financing table
        Schema::create('peminjaman_invoice_financing', function (Blueprint $table) {
            $table->ulid('id_invoice_financing')->primary()->unique();

            $table->string('nomor_peminjaman')->nullable();

            $table->ulid('id_debitur');
            $table->ulid('id_instansi')->nullable();

            $table->enum('sumber_pembiayaan', ['eksternal','internal'])->default('eksternal');
            $table->enum('nama_bank', ['BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'])->nullable();
            $table->string('no_rekening', 100)->nullable();
            $table->string('nama_rekening', 255)->nullable();
            $table->string('lampiran_sid', 255)->nullable();
            $table->string('tujuan_pembiayaan', 255)->nullable();
            $table->decimal('total_pinjaman', 15, 2)->default(0);
            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->decimal('total_bagi_hasil', 15, 2)->default(0);
            $table->date('rencana_tgl_pembayaran')->nullable();
            $table->decimal('pembayaran_total', 15, 2)->default(0);
            $table->text('catatan_lainnya')->nullable();
            $table->string('status', 50)->default('draft');
            $table->ulid('created_by')->nullable();
            $table->ulid('updated_by')->nullable();
            $table->timestamps();

            $table->index('id_debitur');
            $table->index('status');

            $table->foreign('id_debitur')->references('id_debitur')->on('master_debitur_dan_investor')->onUpdate('cascade')->onDelete('restrict');
            if (Schema::hasTable('master_sumber_pendanaan_eksternal')) {
                $table->foreign('id_instansi')->references('id_instansi')->on('master_sumber_pendanaan_eksternal')->onUpdate('cascade')->onDelete('set null');
            }
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // invoice financing table
        Schema::create('invoice_financing', function (Blueprint $table) {
            $table->ulid('id_invoice')->primary()->unique();
            $table->string('no_invoice', 255)->index();
            $table->string('nama_client', 255)->nullable();
            $table->decimal('nilai_invoice', 15, 2)->default(0);
            $table->decimal('nilai_pinjaman', 15, 2)->default(0);
            $table->decimal('nilai_bagi_hasil', 15, 2)->default(0);
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('dokumen_invoice', 255)->nullable();
            $table->string('dokumen_kontrak', 255)->nullable();
            $table->string('dokumen_so', 255)->nullable();
            $table->string('dokumen_bast', 255)->nullable();
            $table->ulid('created_by')->nullable();
            $table->timestamps();
            $table->ulid('id_invoice_financing')->index();
            $table->foreign('id_invoice_financing')->references('id_invoice_financing')->on('peminjaman_invoice_financing')->onUpdate('cascade')->onDelete('cascade');

            // old id_peminjaman foreign removed
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // peminjaman po financing table
        Schema::create('peminjaman_po_financing', function (Blueprint $table) {
            $table->ulid('id_po_financing')->primary()->unique();

            $table->string('nomor_peminjaman')->nullable();

            $table->ulid('id_debitur')->index();
            $table->ulid('id_instansi')->nullable();
            $table->string('no_kontrak', 255)->unique();
            $table->enum('nama_bank', [
                'BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank',
                'OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'
            ])->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('nama_rekening', 255)->nullable();
            $table->string('lampiran_sid', 255)->nullable();
            $table->string('tujuan_pembiayaan', 255)->nullable();
            $table->decimal('total_pinjaman', 15, 2);
            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->decimal('total_bagi_hasil', 15, 2);
            $table->date('rencana_tgl_pembayaran')->nullable();
            $table->decimal('pembayaran_total', 15, 2);
            $table->string('catatan_lainnya', 255)->nullable();
            $table->string('status')->default('draft');
            $table->enum('sumber_pembiayaan', ['eksternal','internal']);
            $table->ulid('created_by')->nullable();
            $table->ulid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('id_debitur')->references('id_debitur')->on('master_debitur_dan_investor')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            if (Schema::hasTable('master_sumber_pendanaan_eksternal')) {
                $table->foreign('id_instansi')->references('id_instansi')->on('master_sumber_pendanaan_eksternal')->onUpdate('cascade')->onDelete('set null');
            }
        });

        // po financing table
        Schema::create('po_financing', function (Blueprint $table) {
            $table->ulid('id_po_financing_detail')->primary()->unique();
            $table->ulid('id_po_financing')->index();

            $table->string('no_kontrak', 255);
            $table->string('nama_client', 255)->nullable();
            $table->decimal('nilai_invoice', 15, 2);
            $table->decimal('nilai_pinjaman', 15, 2);
            $table->decimal('nilai_bagi_hasil', 15, 2);
            $table->date('kontrak_date');
            $table->date('due_date');
            $table->string('dokumen_kontrak', 255)->nullable();
            $table->string('dokumen_so', 255)->nullable();
            $table->string('dokumen_bast', 255)->nullable();
            $table->string('dokumen_lainnya', 255)->nullable();
            $table->ulid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('id_po_financing')->references('id_po_financing')->on('peminjaman_po_financing')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // peminjaman factoring table
        Schema::create('peminjaman_factoring', function (Blueprint $table) {
            $table->ulid('id_factoring')->primary()->unique();
            $table->string('nomor_peminjaman')->nullable();

            $table->ulid('id_debitur');
            $table->enum('nama_bank', ['BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'])->nullable();
            $table->string('no_rekening', 50)->nullable();
            $table->string('nama_rekening')->nullable();
            $table->decimal('total_nominal_yang_dialihkan', 15, 2)->default(0.00);
            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->decimal('total_bagi_hasil', 15, 2)->default(0.00);
            $table->date('rencana_tgl_pembayaran')->nullable();
            $table->decimal('pembayaran_total', 15, 2)->default(0.00);
            $table->string('catatan_lainnya')->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamps();
        });

        // factoring_details table
        Schema::create('factoring_financing', function (Blueprint $table) {
            $table->ulid('id_factoring_detail')->primary()->unique();
            $table->ulid('id_factoring');
            $table->string('no_kontrak')->nullable();
            $table->string('nama_client')->nullable();
            $table->decimal('nilai_invoice', 15, 2)->default(0.00);
            $table->decimal('nilai_pinjaman', 15, 2)->default(0.00);
            $table->decimal('nilai_bagi_hasil', 15, 2)->default(0.00);
            $table->date('kontrak_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('dokumen_invoice', 191)->nullable();
            $table->string('dokumen_so', 191)->nullable();
            $table->string('dokumen_bast', 191)->nullable();
            $table->string('dokumen_kontrak', 191)->nullable();
            $table->timestamps();

            $table->foreign('id_factoring')->references('id_factoring')->on('peminjaman_factoring')->onDelete('cascade');
        });

        // peminjaman_installment_financing table
        Schema::create('peminjaman_installment_financing', function (Blueprint $table) {
            $table->ulid('id_installment')->primary()->unique();
            $table->string('nomor_peminjaman')->nullable();

            $table->ulid('id_debitur')->index();
            $table->enum('nama_bank', ['BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'])->nullable();
            $table->string('no_rekening', 100)->nullable();
            $table->string('nama_rekening')->nullable();
            $table->decimal('total_pinjaman', 15, 2)->default(0);
            $table->enum('tenor_pembayaran', ['3','6','9','12']);
            $table->decimal('persentase_bagi_hasil', 8, 4)->nullable();
            $table->decimal('pps', 15, 2)->nullable();
            $table->decimal('sfinance', 15, 2)->nullable();
            $table->decimal('total_pembayaran', 15, 2)->nullable();
            $table->string('status')->default('draft');
            $table->decimal('yang_harus_dibayarkan', 15, 2)->nullable();
            $table->string('catatan_lainnya')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // installment_financing table
        Schema::create('installment_financing', function (Blueprint $table) {
            $table->ulid('id_installment_detail')->primary()->unique();
            $table->ulid('id_installment')->index();
            $table->string('no_invoice');
            $table->string('nama_client')->nullable();
            $table->decimal('nilai_invoice', 15, 2)->default(0);
            $table->date('invoice_date')->nullable();
            $table->string('nama_barang')->nullable();
            $table->string('dokumen_invoice')->nullable();
            $table->string('dokumen_lainnya')->nullable();
            $table->timestamps();

            $table->foreign('id_installment')
                ->references('id_installment')
                ->on('peminjaman_installment_financing')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        // master karyawan ski table
        Schema::create('master_karyawan_ski', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->ulid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('nama_karyawan');
            $table->string('jabatan');
            $table->string('email')->unique();
            $table->string('role')->nullable();
            $table->string('password');
            $table->enum('status', ['Active', 'Non Active'])->default('Active');
            $table->rememberToken();
            $table->timestamps();
        });

        // form kerja investor table
        Schema::create('form_kerja_investor', function (Blueprint $table) {
            $table->ulid('id_form_kerja_investor')->primary()->unique();

            $table->ulid('id_debitur')->nullable(); // Match with master_debitur_dan_investor
            $table->string('nama_investor');
            $table->enum('deposito', ['reguler', 'khusus']);
            $table->date('tanggal_pembayaran')->nullable();
            $table->integer('lama_investasi')->nullable()->comment('Dalam bulan');
            $table->decimal('jumlah_investasi', 15, 2);
            $table->decimal('bagi_hasil', 5, 2)->comment('Persentase bagi hasil');
            $table->decimal('bagi_hasil_keseluruhan', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('alasan_penolakan')->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->text('keterangan_bukti')->nullable();
            $table->string('nomor_kontrak')->nullable();
            $table->date('tanggal_kontrak')->nullable();
            $table->text('catatan_kontrak')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('id_debitur')
                ->references('id_debitur')
                ->on('master_debitur_dan_investor')
                ->onDelete('cascade');
        });

        // pengajuan_peminjaman table
        Schema::create('pengajuan_peminjaman', function (Blueprint $table) {
            $table->ulid('id_pengajuan_peminjaman')->primary();
            $table->string('nomor_peminjaman')->index()->nullable();

            $table->ulid('id_debitur')->index();
            $table->enum('sumber_pembiayaan', ['eksternal', 'internal']);
            $table->ulid('id_instansi')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('nama_rekening')->nullable();
            $table->string('lampiran_sid')->nullable();
            $table->string('nilai_kol')->nullable();
            $table->text('tujuan_pembiayaan')->nullable();
            $table->enum('jenis_pembiayaan', ['PO Financing', 'Invoice Financing', 'Installment', 'Factoring'])->nullable();
            $table->decimal('total_pinjaman', 15, 2)->nullable();
            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->decimal('total_bagi_hasil', 15, 2)->nullable();
            $table->date('rencana_tgl_pembayaran')->nullable();
            $table->decimal('pembayaran_total', 15, 2)->nullable();
            $table->text('catatan_lainnya')->nullable();
            $table->enum('tenor_pembayaran', ['3', '6', '9', '12'])->nullable();
            $table->decimal('persentase_bagi_hasil', 8, 2)->nullable();
            $table->decimal('pps', 15, 2)->nullable();
            $table->decimal('s_finance', 15, 2)->nullable();
            $table->decimal('yang_harus_dibayarkan', 15, 2)->nullable();
            $table->decimal('total_nominal_yang_dialihkan', 15, 2)->nullable();
            $table->string('status')->nullable();
            $table->ulid('created_by')->nullable();
            $table->ulid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('id_debitur')->references('id_debitur')->on('master_debitur_dan_investor')->onDelete('cascade');
            $table->foreign('id_instansi')->references('id_instansi')->on('master_sumber_pendanaan_eksternal')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        // bukti_peminjaman table
        Schema::create('bukti_peminjaman', function (Blueprint $table) {
            $table->ulid('id_bukti_peminjaman')->primary();
            $table->ulid('id_pengajuan_peminjaman');
            $table->string('no_invoice')->nullable();
            $table->string('no_kontrak')->nullable();
            $table->string('nama_client')->nullable();
            $table->decimal('nilai_invoice', 15, 2)->nullable();
            $table->decimal('nilai_pinjaman', 15, 2)->nullable();
            $table->decimal('nilai_bagi_hasil', 15, 2)->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('dokumen_invoice')->nullable();
            $table->string('dokumen_kontrak')->nullable();
            $table->string('dokumen_so')->nullable();
            $table->string('dokumen_bast')->nullable();
            $table->date('kontrak_date')->nullable();
            $table->string('dokumen_lainnya')->nullable();
            $table->string('nama_barang')->nullable();
            $table->timestamps();


            $table->foreign('id_pengajuan_peminjaman')->references('id_pengajuan_peminjaman')->on('pengajuan_peminjaman')->onUpdate('cascade')->onDelete('cascade');
        });

        // cache table
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropAllTables();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->text('two_factor_secret')
                ->after('password')
                ->nullable();

            $table->text('two_factor_recovery_codes')
                ->after('two_factor_secret')
                ->nullable();

            $table->timestamp('two_factor_confirmed_at')
                ->after('two_factor_recovery_codes')
                ->nullable();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('name');
            $table->boolean('personal_team');
            $table->timestamps();
        });

        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->foreignId('user_id');
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });

        Schema::create('team_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'email']);
        });

        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        throw_if(empty($tableNames), new Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.'));
        throw_if($teams && empty($columnNames['team_foreign_key'] ?? null), new Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.'));

        Schema::create($tableNames['permissions'], static function (Blueprint $table) {
            // $table->engine('InnoDB');
            $table->bigIncrements('id'); // permission id
            $table->string('name');       // For MyISAM use string('name', 225); // (or 166 for InnoDB with Redundant/Compact row format)
            $table->string('guard_name'); // For MyISAM use string('guard_name', 25);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
            // $table->engine('InnoDB');
            $table->bigIncrements('id'); // role id
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name');       // For MyISAM use string('name', 225); // (or 166 for InnoDB with Redundant/Compact row format)
            $table->string('guard_name'); // For MyISAM use string('guard_name', 25);
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
            $table->unsignedBigInteger($pivotPermission);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            } else {
                $table->primary(
                    [$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            }
        });

        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
            $table->unsignedBigInteger($pivotRole);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            } else {
                $table->primary(
                    [$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            }
        });

        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        try {
            app('cache')
                ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
                ->forget(config('permission.cache.key'));
        } catch (\Exception $e) {
            // Cache table might not exist yet during fresh migrations
            // This is safe to ignore as the cache will be cleared when needed
        }

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('master_kol', function (Blueprint $table) {
            $table->increments('id_kol');
            $table->integer('kol');
            $table->decimal('persentase_pencairan', 5, 2)->default(0);
            $table->integer('jmlh_hari_keterlambatan')->default(0);
            $table->timestamps();
        });

        Schema::create('master_sumber_pendanaan_eksternal', function (Blueprint $table) {
            $table->increments('id_instansi');
            $table->string('nama_instansi', 255);
            $table->integer('persentase_bagi_hasil')->default(0);
            $table->timestamps();
        });

        Schema::create('master_debitur_dan_investor', function (Blueprint $table) {
            $table->increments('id_debitur');

            $table->unsignedInteger('id_kol');

            $table->string('nama_debitur', 255);
            $table->string('alamat', 255)->nullable();
            $table->string('email', 255)->nullable();

            $table->string('nama_ceo', 255)->nullable();
            $table->string('nama_bank', 255)->nullable();
            $table->string('no_rek', 100)->nullable();

            $table->timestamps();

            $table->foreign('id_kol')
                  ->references('id_kol')->on('master_kol')
                  ->onDelete('restrict')->onUpdate('cascade');

        });

        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->enum('flagging', ['ya', 'tidak'])->default('tidak')->after('nama_debitur');
        });

        Schema::create('pengembalian_pinjamen', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('config_matrix_pinjaman', function (Blueprint $table) {
            $table->increments('id_matrix_pinjaman');
            $table->decimal('nominal', 15, 2);
            $table->string('approve_oleh', 255)->nullable();
            $table->timestamps();
        });

        $banks = [
            'BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'
        ];

        $enumList = implode("','", array_map(function($s){ return str_replace("'","\\'", $s); }, $banks));

        DB::statement("ALTER TABLE `master_debitur_dan_investor` MODIFY `nama_bank` ENUM('".$enumList."') NULL");

        Schema::create('peminjaman_invoice_financing', function (Blueprint $table) {
            $table->increments('id_invoice_financing');
            $table->unsignedInteger('id_debitur');
            $table->unsignedInteger('id_instansi')->nullable();
            $table->enum('sumber_pembiayaan', ['eksternal','internal'])->default('eksternal');
            $table->enum('nama_bank', ['BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'])->nullable();
            $table->string('no_rekening', 100)->nullable();
            $table->string('nama_rekening', 255)->nullable();
            $table->string('lampiran_sid', 255)->nullable();
            $table->string('tujuan_pembiayaan', 255)->nullable();
            $table->decimal('total_pinjaman', 15, 2)->default(0);
            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->decimal('total_bagi_hasil', 15, 2)->default(0);
            $table->date('rencana_tgl_pembayaran')->nullable();
            $table->decimal('pembayaran_total', 15, 2)->default(0);
            $table->text('catatan_lainnya')->nullable();
            $table->string('status', 50)->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('id_debitur');
            $table->index('status');

            $table->foreign('id_debitur')->references('id_debitur')->on('master_debitur_dan_investor')->onUpdate('cascade')->onDelete('restrict');
            if (Schema::hasTable('master_sumber_pendanaan_eksternal')) {
                $table->foreign('id_instansi')->references('id_instansi')->on('master_sumber_pendanaan_eksternal')->onUpdate('cascade')->onDelete('set null');
            }
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('invoice_financing', function (Blueprint $table) {
            $table->increments('id_invoice');
            $table->string('no_invoice', 255);
            $table->string('nama_client', 255)->nullable();
            $table->decimal('nilai_invoice', 15, 2)->default(0);
            $table->decimal('nilai_pinjaman', 15, 2)->default(0);
            $table->decimal('nilai_bagi_hasil', 15, 2)->default(0);
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('dokumen_invoice', 255)->nullable();
            $table->string('dokumen_kontrak', 255)->nullable();
            $table->string('dokumen_so', 255)->nullable();
            $table->string('dokumen_bast', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->unsignedInteger('id_invoice_financing');
            $table->index('id_invoice_financing');
            $table->index('no_invoice');
            $table->foreign('id_invoice_financing')->references('id_invoice_financing')->on('peminjaman_invoice_financing')->onUpdate('cascade')->onDelete('cascade');

            // old id_peminjaman foreign removed
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('peminjaman_po_financing', function (Blueprint $table) {
            $table->increments('id_po_financing');
            $table->unsignedInteger('id_debitur');
            $table->unsignedInteger('id_instansi')->nullable();
            $table->string('no_kontrak', 255)->unique();
            $table->enum('nama_bank', [
                'BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank',
                'OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'
            ])->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('nama_rekening', 255)->nullable();
            $table->string('lampiran_sid', 255)->nullable();
            $table->string('tujuan_pembiayaan', 255)->nullable();
            $table->decimal('total_pinjaman', 15, 2);
            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->decimal('total_bagi_hasil', 15, 2);
            $table->date('rencana_tgl_pembayaran')->nullable();
            $table->float('pembayaran_total');
            $table->string('catatan_lainnya', 255)->nullable();
            $table->string('status')->default('draft');
            $table->enum('sumber_pembiayaan', ['eksternal','internal']);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('id_debitur');
            $table->index('status');
            $table->foreign('id_debitur')->references('id_debitur')->on('master_debitur_dan_investor')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            if (Schema::hasTable('master_sumber_pendanaan_eksternal')) {
                $table->foreign('id_instansi')->references('id_instansi')->on('master_sumber_pendanaan_eksternal')->onUpdate('cascade')->onDelete('set null');
            }
        });

        Schema::create('po_financing', function (Blueprint $table) {
            $table->increments('id_po_financing_detail');
            $table->unsignedInteger('id_po_financing');
            $table->string('no_kontrak', 255);
            $table->string('nama_client', 255)->nullable();
            $table->decimal('nilai_invoice', 15, 2);
            $table->decimal('nilai_pinjaman', 15, 2);
            $table->decimal('nilai_bagi_hasil', 15, 2);
            $table->date('kontrak_date');
            $table->date('due_date');
            $table->string('dokumen_kontrak', 255)->nullable();
            $table->string('dokumen_so', 255)->nullable();
            $table->string('dokumen_bast', 255)->nullable();
            $table->string('dokumen_lainnya', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('id_po_financing');
            $table->index('no_kontrak');
            $table->foreign('id_po_financing')->references('id_po_financing')->on('peminjaman_po_financing')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        DB::statement("ALTER TABLE `peminjaman_po_financing` MODIFY `pembayaran_total` DECIMAL(15,2) NOT NULL");

        Schema::create('peminjaman_factoring', function (Blueprint $table) {
            $table->bigIncrements('id_factoring');
            $table->unsignedBigInteger('id_debitur');
            $table->enum('nama_bank', ['BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'])->nullable();
            $table->string('no_rekening', 50)->nullable();
            $table->string('nama_rekening')->nullable();
            $table->decimal('total_nominal_yang_dialihkan', 15, 2)->default(0.00);
            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->decimal('total_bagi_hasil', 15, 2)->default(0.00);
            $table->date('rencana_tgl_pembayaran')->nullable();
            $table->decimal('pembayaran_total', 15, 2)->default(0.00);
            $table->string('catatan_lainnya')->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('peminjaman_factoring', function (Blueprint $table) {
            $table->bigIncrements('id_factoring');
            $table->unsignedBigInteger('id_debitur');
            $table->enum('nama_bank', ['BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'])->nullable();
            $table->string('no_rekening', 50)->nullable();
            $table->string('nama_rekening')->nullable();
            $table->decimal('total_nominal_yang_dialihkan', 15, 2)->default(0.00);
            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->decimal('total_bagi_hasil', 15, 2)->default(0.00);
            $table->date('rencana_tgl_pembayaran')->nullable();
            $table->decimal('pembayaran_total', 15, 2)->default(0.00);
            $table->string('catatan_lainnya')->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('factoring_details') && !Schema::hasTable('factoring_financing')) {
            Schema::rename('factoring_details', 'factoring_financing');
        }

        Schema::create('peminjaman_installment_financing', function (Blueprint $table) {
            $table->id('id_installment');
            $table->unsignedBigInteger('id_debitur');
            $table->enum('nama_bank', ['BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'])->nullable();
            $table->string('no_rekening', 100)->nullable();
            $table->string('nama_rekening')->nullable();
            $table->decimal('total_pinjaman', 15, 2)->default(0);
            $table->enum('tenor_pembayaran', ['3','6','9','12']);
            $table->decimal('persentase_bagi_hasil', 8, 4)->nullable();
            $table->decimal('pps', 15, 2)->nullable();
            $table->decimal('sfinance', 15, 2)->nullable();
            $table->decimal('total_pembayaran', 15, 2)->nullable();
            $table->string('status')->default('draft');
            $table->decimal('yang_harus_dibayarkan', 15, 2)->nullable();
            $table->string('catatan_lainnya')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('id_debitur');
            $table->index('status');
        });

        Schema::create('installment_financing', function (Blueprint $table) {
            $table->id('id_installment_detail');
            $table->unsignedBigInteger('id_installment');
            $table->string('no_invoice');
            $table->string('nama_client')->nullable();
            $table->decimal('nilai_invoice', 15, 2)->default(0);
            $table->date('invoice_date')->nullable();
            $table->string('nama_barang')->nullable();
            $table->string('dokumen_invoice')->nullable();
            $table->string('dokumen_lainnya')->nullable();
            $table->timestamps();

            $table->index('id_installment');
            $table->index('no_invoice');

            $table->foreign('id_installment')
                ->references('id_installment')
                ->on('peminjaman_installment_financing')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            // Nomor Telepon
            $table->string('no_telepon', 20)->nullable()->after('email');
            
            // Status: active atau non active
            $table->enum('status', ['active', 'non active'])->default('active')->after('no_telepon');
            
            // Deposito: reguler atau khusus (khusus untuk investor)
            $table->enum('deposito', ['reguler', 'khusus'])->nullable()->after('status');
        });

        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id_debitur')->constrained('users')->onDelete('cascade');
        });

        Schema::table('peminjaman_invoice_financing', function (Blueprint $table) {
            $table->string('nomor_peminjaman')->nullable()->after('id_invoice_financing')->index();
        });

        Schema::table('peminjaman_po_financing', function (Blueprint $table) {
            $table->string('nomor_peminjaman')->nullable()->after('id_po_financing')->index();
        });

        Schema::table('peminjaman_installment_financing', function (Blueprint $table) {
            $table->string('nomor_peminjaman')->nullable()->after('id_installment')->index();
        });

        Schema::table('peminjaman_factoring', function (Blueprint $table) {
            $table->string('nomor_peminjaman')->nullable()->after('id_factoring')->index();
        });

        Schema::create('master_karyawan_ski', function (Blueprint $table) {
            $table->id();
            $table->string('nama_karyawan');
            $table->string('jabatan');
            $table->string('email')->unique();
            $table->string('role')->nullable();
            $table->string('password');
            $table->enum('status', ['Active', 'Non Active'])->default('Active');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('master_karyawan_ski', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
        });

        Schema::create('form_kerja_investor', function (Blueprint $table) {
            $table->id('id_form_kerja_investor');
            $table->unsignedInteger('id_debitur'); // Match with master_debitur_dan_investor
            $table->string('nama_investor');
            $table->enum('deposito', ['reguler', 'khusus']);
            $table->date('tanggal_pembayaran')->nullable();
            $table->integer('lama_investasi')->nullable()->comment('Dalam bulan');
            $table->decimal('jumlah_investasi', 15, 2);
            $table->decimal('bagi_hasil', 5, 2)->comment('Persentase bagi hasil');
            $table->decimal('bagi_hasil_keseluruhan', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('alasan_penolakan')->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->text('keterangan_bukti')->nullable();
            $table->string('nomor_kontrak')->nullable();
            $table->date('tanggal_kontrak')->nullable();
            $table->text('catatan_kontrak')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('id_debitur')
                ->references('id_debitur')
                ->on('master_debitur_dan_investor')
                ->onDelete('cascade');
        });

        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->renameColumn('nama_debitur', 'nama');
        });

        Schema::create('pengajuan_peminjaman', function (Blueprint $table) {
            $table->ulid('id_pengajuan_peminjaman')->primary();
            $table->string('nomor_peminjaman')->index()->nullable();
            $table->unsignedInteger('id_debitur');
            $table->enum('sumber_pembiayaan', ['eksternal', 'internal']);
            $table->unsignedInteger('id_instansi')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('nama_rekening')->nullable();
            $table->string('lampiran_sid')->nullable();
            $table->string('nilai_kol')->nullable();
            $table->text('tujuan_pembiayaan')->nullable();
            $table->enum('jenis_pembiayaan', ['PO Financing', 'Invoice Financing', 'Installment', 'Factoring'])->nullable();
            $table->decimal('total_pinjaman', 15, 2)->nullable();
            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->decimal('total_bagi_hasil', 15, 2)->nullable();
            $table->date('rencana_tgl_pembayaran')->nullable();
            $table->decimal('pembayaran_total', 15, 2)->nullable();
            $table->text('catatan_lainnya')->nullable();
            $table->enum('tenor_pembayaran', ['3', '6', '9', '12'])->nullable();
            $table->decimal('persentase_bagi_hasil', 8, 2)->nullable();
            $table->decimal('pps', 15, 2)->nullable();
            $table->decimal('s_finance', 15, 2)->nullable();
            $table->decimal('yang_harus_dibayarkan', 15, 2)->nullable();
            $table->decimal('total_nominal_yang_dialihkan', 15, 2)->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('id_debitur');
            $table->index('status');

            $table->foreign('id_debitur')->references('id_debitur')->on('master_debitur_dan_investor')->onDelete('cascade');
            $table->foreign('id_instansi')->references('id_instansi')->on('master_sumber_pendanaan_eksternal')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->unsignedInteger('id_kol')->nullable()->change();
        });

        Schema::create('bukti_peminjaman', function (Blueprint $table) {
            $table->ulid('id_bukti_peminjaman')->primary();
            $table->ulid('id_pengajuan_peminjaman');
            $table->string('no_invoice')->nullable();
            $table->string('no_kontrak')->nullable();
            $table->string('nama_client')->nullable();
            $table->decimal('nilai_invoice', 15, 2)->nullable();
            $table->decimal('nilai_pinjaman', 15, 2)->nullable();
            $table->decimal('nilai_bagi_hasil', 15, 2)->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('dokumen_invoice')->nullable();
            $table->string('dokumen_kontrak')->nullable();
            $table->string('dokumen_so')->nullable();
            $table->string('dokumen_bast')->nullable();
            $table->date('kontrak_date')->nullable();
            $table->string('dokumen_lainnya')->nullable();
            $table->string('nama_barang')->nullable();
            $table->timestamps();


            $table->foreign('id_pengajuan_peminjaman')->references('id_pengajuan_peminjaman')->on('pengajuan_peminjaman')->onUpdate('cascade')->onDelete('cascade');

        });

        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->string('tanda_tangan', 255)->nullable()->after('flagging');
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }
};
