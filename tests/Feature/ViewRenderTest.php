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
        
        $pAdmin = Peran::create(['nama_peran' => 'admin']);
        $pSpv   = Peran::create(['nama_peran' => 'supervisor']);
        $pCs    = Peran::create(['nama_peran' => 'cs']);
        $pGdg   = Peran::create(['nama_peran' => 'gudang']);

        $area1 = Area::create(['nama_ruangan' => 'Lobi Utama', 'lantai' => 1]);

        $this->admin = User::create(['name' => 'Admin', 'nik' => '111', 'password' => 'pass', 'peran_id' => $pAdmin->id]);
        $this->spv = User::create(['name' => 'Spv', 'nik' => '222', 'password' => 'pass', 'peran_id' => $pSpv->id]);
        $this->cs = User::create(['name' => 'CS', 'nik' => '333', 'password' => 'pass', 'peran_id' => $pCs->id, 'area_id' => $area1->id]);
        $this->gdg = User::create(['name' => 'Gudang', 'nik' => '444', 'password' => 'pass', 'peran_id' => $pGdg->id]);
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
        $this->actingAs($this->spv)->get(route('penilaian.index'))->assertStatus(200);
        $this->actingAs($this->spv)->get(route('penilaian.buat', $this->cs->id))->assertStatus(200);
        $this->actingAs($this->spv)->get(route('penilaian.detail', $this->cs->id))->assertStatus(200);
        $this->actingAs($this->spv)->get(route('penilaian.rekap'))->assertStatus(200);
    }

    public function test_modul_6_laporan_renders()
    {
        $this->actingAs($this->spv)->get(route('laporan.index'))->assertStatus(200);
    }
}
