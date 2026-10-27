<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Area;
use App\Models\Peran;

class ViewRenderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $pAdmin = Peran::firstOrCreate(['nama_peran' => 'admin']);
        $pSpv   = Peran::firstOrCreate(['nama_peran' => 'supervisor']);
        $pCs    = Peran::firstOrCreate(['nama_peran' => 'cs']);
        $pGdg   = Peran::firstOrCreate(['nama_peran' => 'gudang']);

        $area1 = Area::firstOrCreate(['lantai' => 'Lantai 1']);

        $this->admin = User::firstOrCreate(['nik' => '111'], ['name' => 'Admin', 'password' => 'pass', 'peran_id' => $pAdmin->id]);
        $this->spv   = User::firstOrCreate(['nik' => '222'], ['name' => 'Spv', 'password' => 'pass', 'peran_id' => $pSpv->id]);
        $this->cs    = User::firstOrCreate(['nik' => '333'], ['name' => 'CS', 'password' => 'pass', 'peran_id' => $pCs->id]);
        $this->gdg   = User::firstOrCreate(['nik' => '444'], ['name' => 'Gudang', 'password' => 'pass', 'peran_id' => $pGdg->id]);
    }

    public function test_dasbor_admin_renders()
    {
        $this->actingAs($this->admin)->get(route('dasbor.admin'))->assertStatus(200);
    }

    public function test_dasbor_supervisor_renders()
    {
        $this->actingAs($this->spv)->get(route('dasbor.supervisor'))->assertStatus(200);
    }

    public function test_dasbor_cs_renders()
    {
        $this->actingAs($this->cs)->get(route('dasbor.cs'))->assertStatus(200);
    }
    
    public function test_dasbor_gudang_renders()
    {
        $this->actingAs($this->gdg)->get(route('dasbor.gudang'))->assertStatus(200);
    }

    public function test_modul_5_penilaian_renders()
    {
        $this->actingAs($this->admin)->get(route('admin.penilaian-pj.index'))->assertStatus(200);
    }

    public function test_modul_6_laporan_renders()
    {
        $this->actingAs($this->spv)->get(route('laporan.index'))->assertStatus(200);
    }

    public function test_penilaian_pj_public_renders()
    {
        $this->get(route('penilaian-pj.form'))->assertStatus(200);
        $this->get(route('penilaian-pj.sukses'))->assertStatus(200);
    }

    public function test_admin_penilaian_pj_renders()
    {
        $this->actingAs($this->admin)->get(route('admin.penilaian-pj.index'))->assertStatus(200);
    }

    public function test_tugas_mingguan_renders()
    {
        $this->actingAs($this->cs)->get(route('tugas-mingguan.index'))->assertStatus(200);
        $this->actingAs($this->cs)->get(route('tugas-mingguan.buat'))->assertStatus(200);
        $this->actingAs($this->spv)->get(route('tugas-mingguan.index'))->assertStatus(200);
    }

    public function test_stok_barang_renders()
    {
        $this->actingAs($this->gdg)->get(route('stok-barang.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('stok-barang.index'))->assertStatus(200);
    }

    public function test_sampah_renders()
    {
        $this->actingAs($this->cs)->get(route('sampah.buat'))->assertStatus(200);
        $this->actingAs($this->spv)->get(route('sampah.rekapan'))->assertStatus(200);
    }

    public function test_operan_renders()
    {
        $this->actingAs($this->cs)->get(route('operan.index'))->assertStatus(200);
    }

    public function test_export_excel_routes()
    {
        $this->actingAs($this->spv)->get(route('tugas-mingguan.export-excel'))->assertStatus(200);
        $this->actingAs($this->spv)->get(route('operan.export-excel'))->assertStatus(200);
        $this->actingAs($this->spv)->get(route('sampah.export-excel'))->assertStatus(200);
        $this->actingAs($this->gdg)->get(route('barang.export-excel'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('admin.penilaian-pj.export-excel'))->assertStatus(200);
        $this->actingAs($this->gdg)->get(route('stok-barang.export-excel'))->assertStatus(200);
        $this->actingAs($this->spv)->get(route('laporan.cetak-excel'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('stok-keluar.export-excel'))->assertStatus(200);
        $this->actingAs($this->gdg)->get(route('stok-keluar.export-excel'))->assertStatus(200);
    }

    public function test_barang_renders()
    {
        $this->actingAs($this->cs)->get(route('barang.katalog'))->assertStatus(200);
        $this->actingAs($this->spv)->get(route('barang.supervisor'))->assertStatus(200);
        $this->actingAs($this->gdg)->get(route('barang.gudang'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('barang.gudang'))->assertStatus(200);
        $this->actingAs($this->gdg)->get(route('stok-keluar.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('stok-keluar.index'))->assertStatus(200);
    }

    public function test_alur_persetujuan_permintaan_barang()
    {
        $barang = \App\Models\BarangInventori::create([
            'nama_barang'   => 'Sapu Lantai Nilon Test',
            'kode_barang'   => 'SPU-01',
            'satuan'        => 'pcs',
            'stok_saat_ini' => 10,
            'stok_minimum'  => 2,
        ]);

        // 1. CS mengajukan permintaan barang
        $resp = $this->actingAs($this->cs)->post(route('barang.ajukan'), [
            'barang_id' => $barang->id,
            'jumlah'    => 2,
        ]);
        dump($resp->getStatusCode(), substr($resp->getContent(), 0, 500));
        $resp->assertSessionHas('sukses');

        $permintaan = \App\Models\PermintaanBarang::where('barang_id', $barang->id)
            ->where('user_id', $this->cs->id)
            ->first();
        $this->assertNotNull($permintaan);
        $this->assertEquals('menunggu_spv', $permintaan->status_request);

        // 2. Supervisor menyetujui permintaan
        $respSpv = $this->actingAs($this->spv)->patch(route('barang.setujui-spv', $permintaan->id));
        $respSpv->assertSessionHas('sukses');

        $permintaan->refresh();
        $this->assertEquals('disetujui_spv', $permintaan->status_request);
        $this->assertEquals($this->spv->id, $permintaan->supervisor_id);
        $this->assertNotNull($permintaan->waktu_approve_spv);

        // 3. Gudang menyetujui permintaan dan memotong stok
        $respGdg = $this->actingAs($this->gdg)->patch(route('barang.setujui', $permintaan->id));
        $respGdg->assertSessionHas('sukses');

        $permintaan->refresh();
        $this->assertEquals('disetujui', $permintaan->status_request);
        $this->assertNotNull($permintaan->waktu_approve);
        $this->assertEquals(8, $barang->fresh()->stok_saat_ini);
    }

    public function test_admin_master_data_renders()
    {
        $this->actingAs($this->admin)->get(route('admin.pengguna.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('admin.pengguna.create'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('admin.area.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('admin.area.create'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('admin.barang.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('admin.barang.create'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('profil.ganti-password'))->assertStatus(200);
    }

    public function test_area_delete_safely()
    {
        $area = Area::create(['lantai' => 'Lantai 6']);
        $response = $this->actingAs($this->admin)->delete(route('admin.area.destroy', $area->id));
        $response->assertRedirect(route('admin.area.index'));
    }

    public function test_stok_keluar_store_and_bukti()
    {
        $barang = \App\Models\BarangInventori::firstOrCreate(
            ['nama_barang' => 'Cairan Pembersih Lantai Test'],
            ['kode_barang' => 'TST-01', 'satuan' => 'botol', 'stok_saat_ini' => 50, 'stok_minimum' => 5]
        );
        $barang->update(['stok_saat_ini' => 50]);

        $response = $this->actingAs($this->gdg)->post(route('stok-keluar.store'), [
            'barang_id'     => $barang->id,
            'tanggal'       => now()->toDateString(),
            'jumlah_keluar' => 2,
            'unit'          => 'Lantai 1',
            'penerima'      => 'Petugas CS',
            'keterangan'    => 'Pengujian distribusi',
        ]);
        $response->assertRedirect(route('stok-keluar.index'));
        $this->assertEquals(48, $barang->fresh()->stok_saat_ini);

        $mutasi = \App\Models\MutasiStok::where('barang_id', $barang->id)->where('jumlah_keluar', 2)->latest()->first();
        $this->assertNotNull($mutasi);
        $this->actingAs($this->admin)->get(route('stok-keluar.bukti', $mutasi->id))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('stok-keluar.cetak-pdf'))->assertStatus(200);
    }
}
