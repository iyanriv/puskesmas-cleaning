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

    public function test_ceklis_renders()
    {
        $this->actingAs($this->cs)->get(route('ceklis.index'))->assertStatus(200);
    }

    public function test_operan_renders()
    {
        $this->actingAs($this->cs)->get(route('operan.index'))->assertStatus(200);
    }

    public function test_barang_renders()
    {
        $this->actingAs($this->cs)->get(route('barang.katalog'))->assertStatus(200);
        $this->actingAs($this->gdg)->get(route('barang.gudang'))->assertStatus(200);
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
}
