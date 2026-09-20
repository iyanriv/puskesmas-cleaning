<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Penilaian Kinerja Cleaning Service PKM Cempaka Putih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Google+Sans:wght@400;500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f0f4f2;
            color: #202124;
            margin: 0;
            padding: 24px 12px 60px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .gform-container {
            width: 100%;
            max-width: 680px;
        }

        /* ── Google Form Header Card ── */
        .gform-card {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #dadce0;
            border-top: 10px solid #064e2b;
            padding: 28px 24px;
            box-shadow: 0 1px 3px 0 rgba(60,64,67,0.15), 0 2px 6px 2px rgba(60,64,67,0.08);
            margin-bottom: 16px;
        }

        .gform-title {
            font-family: 'Google Sans', 'Roboto', Arial, sans-serif;
            font-size: 28px;
            font-weight: 400;
            line-height: 1.35;
            color: #202124;
            margin-bottom: 14px;
        }

        .gform-msg {
            font-size: 15px;
            line-height: 1.5;
            color: #202124;
            margin-bottom: 24px;
        }

        .gform-link {
            display: inline-block;
            color: #1a73e8;
            text-decoration: underline;
            font-size: 14px;
            line-height: 1.5;
            transition: color 0.15s ease;
        }
        .gform-link:hover {
            color: #1557b0;
        }

        /* ── Receipt Summary Box ── */
        .gform-summary-card {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 20px 24px;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.08);
            margin-bottom: 24px;
        }

        .gform-footer {
            text-align: center;
            font-size: 12px;
            color: #70757a;
            line-height: 1.8;
            margin-top: 12px;
        }
        .gform-footer a {
            color: #70757a;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="gform-container">

    {{-- ================================================================
         KARTU UTAMA — PERSIS GOOGLE FORM SELESAI
         ================================================================ --}}
    <div class="gform-card">
        <div class="d-flex align-items-center gap-2 mb-2 text-muted" style="font-size: 13px;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 22px; width: auto;" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Logo_Puskesmas.png/480px-Logo_Puskesmas.png'">
            <span>Puskesmas Kecamatan Cempaka Putih</span>
        </div>

        <h1 class="gform-title">
            Formulir Penilaian Kinerja Cleaning Service PKM Cempaka Putih
        </h1>

        <div class="gform-msg">
            Tanggapan Anda telah direkam.
        </div>

        <div>
            <a href="{{ route('penilaian-pj.form') }}" class="gform-link">
                Kirim tanggapan lain
            </a>
        </div>
    </div>

    {{-- ================================================================
         RINGKASAN TANGGAPAN YANG TERCATAT (BUKTI PENILAIAN)
         ================================================================ --}}
    @if(session('nama_petugas'))
        <div class="gform-summary-card">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="fw-semibold text-dark d-flex align-items-center gap-2" style="font-size: 14px;">
                    <i class="bi bi-check-circle-fill text-success"></i> Ringkasan Penilaian
                </div>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1" style="font-size: 11px;">
                    Tersimpan di Sistem
                </span>
            </div>

            <div class="row g-2" style="font-size: 13.5px;">
                <div class="col-sm-5 text-secondary">Nama Petugas CS:</div>
                <div class="col-sm-7 fw-semibold text-dark">{{ session('nama_petugas') }}</div>

                @if(session('nama_pj'))
                    <div class="col-sm-5 text-secondary">Penilai (PJ Kebersihan):</div>
                    <div class="col-sm-7 text-dark">{{ session('nama_pj') }}</div>
                @endif

                @if(session('rata_rata'))
                    <div class="col-sm-5 text-secondary">Rata-rata Skor:</div>
                    <div class="col-sm-7">
                        <span class="fw-bold text-dark">{{ session('rata_rata') }}</span> / 95
                        <span class="badge bg-success ms-1 px-2 py-1" style="font-size: 11px;">
                            {{ session('kategori') }}
                        </span>
                    </div>
                @endif

                <div class="col-sm-5 text-secondary">Waktu Kirim:</div>
                <div class="col-sm-7 text-muted">{{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
            </div>
        </div>
    @endif

    {{-- ================================================================
         FOOTER ALA GOOGLE FORM
         ================================================================ --}}
    <div class="gform-footer">
        <div>Konten ini dibuat untuk sistem monitoring internal Puskesmas Kecamatan Cempaka Putih.</div>
        <div>
            <a href="{{ route('penilaian-pj.form') }}">Isi Formulir Baru</a> &bull;
            <span>SIM Kebersihan &copy; {{ date('Y') }}</span>
        </div>
    </div>

</div>

</body>
</html>

