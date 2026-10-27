<?php

namespace App\Exports;

use App\Models\TugasMingguan;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TugasMingguanExport implements
    FromCollection,
    ShouldAutoSize,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle
{
    protected $dari;
    protected $sampai;
    protected $petugasId;
    protected $status;

    public function __construct($dari, $sampai, $petugasId = null, $status = null)
    {
        $this->dari      = $dari;
        $this->sampai    = $sampai;
        $this->petugasId = $petugasId;
        $this->status    = $status;
    }

    public function title(): string
    {
        return 'Tugas Mingguan';
    }

    public function collection()
    {
        $query = TugasMingguan::with(['user', 'verifikator'])
            ->whereBetween('tanggal', [$this->dari, $this->sampai])
            ->latest('tanggal')
            ->latest('waktu_pelaporan');

        if ($this->petugasId) {
            $query->where('user_id', $this->petugasId);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Waktu Pelaporan',
            'Nama Petugas',
            'Rincian Kegiatan',
            'Status',
            'Diverifikasi Oleh',
            'Catatan Supervisor',
            'Diverifikasi Pada',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $statusLabel = match ($row->status) {
            'disetujui' => 'Disetujui',
            'selesai'   => 'Selesai',
            'proses'    => 'Dalam Proses',
            default     => ucfirst($row->status),
        };

        return [
            $no,
            $row->tanggal,
            $row->waktu_pelaporan,
            $row->user?->name ?? '-',
            $row->rincian_kegiatan,
            $statusLabel,
            $row->verifikator?->name ?? '-',
            $row->catatan_supervisor ?? '-',
            $row->diverifikasi_pada ? \Carbon\Carbon::parse($row->diverifikasi_pada)->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF16A34A'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
