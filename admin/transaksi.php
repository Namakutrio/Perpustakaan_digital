<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Sistem Manajemen Transaksi Perpustakaan</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            font-family: system-ui, 'Segoe UI', 'Inter', 'Roboto', sans-serif;
        }

        body {
            background: #eef2f7;
            padding: 2rem 1.5rem;
        }

        /* container utama */
        .dashboard {
            max-width: 1440px;
            margin: 0 auto;
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
            padding: 1.8rem 2rem 2.5rem 2rem;
        }

        /* Header dengan menu navigasi */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.8rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e2a3e;
            letter-spacing: -0.3px;
            border-left: 5px solid #3b82f6;
            padding-left: 1rem;
            margin: 0;
        }

        /* Menu Navigasi */
        .nav-menu {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-nav {
            padding: 0.6rem 1.2rem;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            background: #f1f5f9;
            color: #1e293b;
        }

        .btn-nav:hover {
            transform: translateY(-2px);
            background: #e2e8f0;
        }

        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .btn-back:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46a0 100%);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-home {
            background: #10b981;
            color: white;
        }

        .btn-home:hover {
            background: #059669;
        }

        /* form pinjam (2 kolom flex) */
        .form-pinjam {
            background: #f8fafc;
            border-radius: 24px;
            padding: 1.6rem 2rem;
            margin-bottom: 2.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem 2rem;
            align-items: flex-end;
            border: 1px solid #e2e8f0;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #334155;
            margin-bottom: 0.5rem;
        }

       
        .form-group select,
        .form-group button {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 16px;
            font-size: 0.95rem;
            font-weight: 500;
            background: white;
            border: 1px solid #cbd5e1;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .form-group select {
            background-color: white;
            color: #0f172a;
        }

        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        /* TOMBOL PINJAM BUKU */
        .btn-pinjam {
            background: #3b82f6;
            color: black;
            border: none;
            font-weight: 600;
            transition: 0.2s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-pinjam:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px -6px rgba(37, 99, 235, 0.3);
            cursor: pointer;
        }

        .btn-pinjam:active {
            transform: translateY(1px);
        }

        /* toolbar filter */
        .toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin: 1.2rem 0 1.5rem 0;
            gap: 1rem;
        }

        .filter-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-left span {
            font-weight: 600;
            color: #1e293b;
        }

        .filter-buttons {
            display: flex;
            gap: 0.75rem;
        }

        .btn-filter {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1.2rem;
            border-radius: 40px;
            font-weight: 500;
            font-size: 0.85rem;
            color: #1e293b;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-filter.active {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }

        .btn-filter.reset-btn {
            background: white;
            border: 1px solid #cbd5e1;
        }

        .btn-filter.reset-btn:hover {
            background: #f1f5f9;
        }

        /* tabel responsive */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 20px;
            border: 1px solid #e9edf2;
            background: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            min-width: 780px;
        }

        th {
            text-align: left;
            padding: 1rem 1rem;
            background-color: #f9fbfd;
            font-weight: 600;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 1rem 1rem;
            border-bottom: 1px solid #eff3f8;
            vertical-align: middle;
            color: #0f172a;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e6f7ec;
            color: #15803d;
            padding: 0.25rem 0.75rem;
            border-radius: 40px;
            font-weight: 500;
            font-size: 0.8rem;
            white-space: nowrap;
        }

      
        .denda-cell {
            font-weight: 500;
            color: #b45309;
            white-space: nowrap;
        }

        
        .aksi-cell {
            font-weight: 500;
            color: #2563eb;
        }

        .info-empty {
            text-align: center;
            padding: 2rem;
            color: #5b6e8c;
            font-style: italic;
        }

        footer {
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: #5b6e8c;
            text-align: right;
            border-top: 1px solid #eef2f8;
            padding-top: 1rem;
        }

        /* Modal Konfirmasi */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 24px;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 20px 35px -12px rgba(0,0,0,0.2);
        }

        .modal-content h3 {
            margin-bottom: 1rem;
            color: #1e2a3e;
        }

        .modal-content p {
            margin-bottom: 1.5rem;
            color: #475569;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .modal-buttons button {
            padding: 0.6rem 1.5rem;
            border-radius: 40px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-confirm {
            background: #3b82f6;
            color: white;
        }

        .btn-cancel {
            background: #e2e8f0;
            color: #1e293b;
        }

        @media (max-width: 720px) {
            .dashboard {
                padding: 1rem;
            }

            .form-pinjam {
                padding: 1rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-menu {
                width: 100%;
            }

            .btn-nav {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <!-- Header dengan Menu Navigasi -->
        <div class="header-section">
            <h1>Manajemen Transaksi</h1>
            <div class="nav-menu">
                <button id="btnBack" class="btn-nav btn-back">
                    ← Kembali ke Halaman Sebelumnya
                </button>
            </div>
        </div>

        <!-- Modal Konfirmasi -->
        <div id="confirmModal" class="modal">
            <div class="modal-content">
                <h3>⚠️ Konfirmasi</h3>
                <p id="modalMessage">Apakah Anda yakin ingin kembali?</p>
                <div class="modal-buttons">
                    <button id="modalConfirm" class="btn-confirm">Ya, Kembali</button>
                    <button id="modalCancel" class="btn-cancel">Batal</button>
                </div>
            </div>
        </div>

        <!-- Form Pinjam Buku -->
        <div class="form-pinjam">
            <div class="form-group">
                <label>Pilih Anggota</label>
                <select id="selectAnggota">
                    <option value="Budi Santoso">Budi Santoso</option>
                    <option value="Trio Aji Saputra">Trio Aji Saputra</option>
                    <option value="Siti Rahma">Siti Rahma</option>
                    <option value="Andi Wijaya">Andi Wijaya</option>
                    <option value="Dewi Sartika">Dewi Sartika</option>
                </select>
            </div>
            <div class="form-group">
                <label>Pilih Buku</label>
                <select id="selectBuku">
                    <option value="London Love Story">London Love Story</option>
                    <option value="Laskar Pelangi">Laskar Pelangi</option>
                    <option value="Negeri 5 Menara">Negeri 5 Menara</option>
                    <option value="Ayat-Ayat Cinta">Ayat-Ayat Cinta</option>
                </select>
            </div>
            <div class="form-group">
                <label> </label>
                <button id="btnPinjamBuku" class="btn-pinjam">Pinjam Buku</button>
            </div>
        </div>

        <!-- Daftar Transaksi + filter -->
        <div class="toolbar">
            <div class="filter-left">
                <span>Daftar Transaksi</span>
                <div class="filter-buttons">
                    <button id="filterSemua" class="btn-filter active">Semua Judul Buku</button>
                    <button id="filterBukuBtn" class="btn-filter">Filter Buku</button>
                    <button id="resetFilterBtn" class="btn-filter reset-btn">Reset Filter</button>
                </div>
            </div>
            <div style="font-size:0.8rem; color:#475569;">
                Menampilkan <span id="jumlahTampil">0</span> transaksi
            </div>
        </div>

        <div class="table-wrapper">
            <table id="transaksiTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                   
                </tbody>
            </table>
        </div>
        <footer>Sistem peminjaman otomatis • Tombol "Pinjam Buku" menambah transaksi baru (status dipinjam, denda 0 sampai dikembalikan)</footer>
    </div>

    <script>
      
        let transactions = [{
                id: 4,
                code: "TRX20260421064230",
                member: "Budi Santoso",
                book: "London Love Story",
                borrowDate: "2026-04-21",
                isReturned: true,
                returnDate: "2026-04-21",
                fine: 0
            },
            {
                id: 3,
                code: "TRX20260421064156",
                member: "Trio Aji Saputra",
                book: "Laskar Pelangi",
                borrowDate: "2026-04-21",
                isReturned: true,
                returnDate: "2026-04-21",
                fine: 0
            },
            {
                id: 2,
                code: "TRX20260421042358",
                member: "Trio Aji Saputra",
                book: "Negeri 5 Menara",
                borrowDate: "2026-04-21",
                isReturned: true,
                returnDate: "2026-04-21",
                fine: 0
            },
            {
                id: 1,
                code: "TRX20260421022900",
                member: "Trio Aji Saputra",
                book: "Ayat-Ayat Cinta",
                borrowDate: "2026-04-21",
                isReturned: true,
                returnDate: "2026-04-21",
                fine: 0
            },
            {
                id: 5,
                code: "TRX20260421091234",
                member: "Siti Rahma",
                book: "Laskar Pelangi",
                borrowDate: "2026-04-20",
                isReturned: true,
                returnDate: "2026-04-21",
                fine: 0
            },
            {
                id: 6,
                code: "TRX20260421084567",
                member: "Andi Wijaya",
                book: "London Love Story",
                borrowDate: "2026-04-19",
                isReturned: true,
                returnDate: "2026-04-21",
                fine: 0
            },
            {
                id: 7,
                code: "TRX20260421075678",
                member: "Dewi Sartika",
                book: "Ayat-Ayat Cinta",
                borrowDate: "2026-04-18",
                isReturned: true,
                returnDate: "2026-04-21",
                fine: 0
            },
            {
                id: 8,
                code: "TRX20260421060123",
                member: "Budi Santoso",
                book: "Negeri 5 Menara",
                borrowDate: "2026-04-17",
                isReturned: true,
                returnDate: "2026-04-21",
                fine: 0
            }
        ];

       
        let nextId = 9;
        let pendingAction = null;

       
        function generateTrxCode() {
            const now = new Date();
            const y = now.getFullYear();
            const m = String(now.getMonth() + 1).padStart(2, '0');
            const d = String(now.getDate()).padStart(2, '0');
            const h = String(now.getHours()).padStart(2, '0');
            const min = String(now.getMinutes()).padStart(2, '0');
            const sec = String(now.getSeconds()).padStart(2, '0');
            const ms = String(now.getMilliseconds()).slice(-2);
            return `TRX${y}${m}${d}${h}${min}${sec}${ms}`;
        }

        
        function calculateFine(borrowDateStr, returnDateStr) {
            if (!returnDateStr) return 0; 
            const borrow = new Date(borrowDateStr);
            const ret = new Date(returnDateStr);
            const diffTime = ret - borrow;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            if (diffDays <= 7) return 0;
            const lateDays = diffDays - 7;
            return lateDays * 2000;
        }

       
        function updateFinesForReturned() {
            for (let t of transactions) {
                if (t.isReturned && t.returnDate) {
                    t.fine = calculateFine(t.borrowDate, t.returnDate);
                } else {
                    if (!t.isReturned) t.fine = 0;
                }
            }
        }

       
        function addTransaction(memberName, bookTitle) {
            const today = new Date().toISOString().slice(0, 10);
            const newCode = generateTrxCode();
            const newTransaction = {
                id: nextId++,
                code: newCode,
                member: memberName,
                book: bookTitle,
                borrowDate: today,
                isReturned: false,
                returnDate: null,
                fine: 0
            };
            transactions.unshift(newTransaction);
            renderTable();
            showToast(`✅ Buku "${bookTitle}" berhasil dipinjam oleh ${memberName}`);
        }

        function returnBook(id) {
            const transaction = transactions.find(t => t.id === id);
            if (!transaction) return;
            if (transaction.isReturned) {
                alert("Buku sudah dikembalikan sebelumnya.");
                return;
            }
            const today = new Date().toISOString().slice(0, 10);
            transaction.isReturned = true;
            transaction.returnDate = today;
            transaction.fine = calculateFine(transaction.borrowDate, today);
            renderTable();
            showToast(`Buku "${transaction.book}" dikembalikan. Denda: Rp ${transaction.fine.toLocaleString()}`);
        }

        function showToast(msg) {
            let toast = document.createElement('div');
            toast.innerText = msg;
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.left = '50%';
            toast.style.transform = 'translateX(-50%)';
            toast.style.backgroundColor = '#1f2937';
            toast.style.color = 'white';
            toast.style.padding = '10px 20px';
            toast.style.borderRadius = '40px';
            toast.style.fontSize = '0.85rem';
            toast.style.zIndex = '999';
            toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }

        // Fungsi Modal
        function showModal(message, onConfirm) {
            const modal = document.getElementById('confirmModal');
            const modalMessage = document.getElementById('modalMessage');
            const confirmBtn = document.getElementById('modalConfirm');
            const cancelBtn = document.getElementById('modalCancel');
            
            modalMessage.textContent = message;
            modal.style.display = 'flex';
            
            const handleConfirm = () => {
                modal.style.display = 'none';
                confirmBtn.removeEventListener('click', handleConfirm);
                cancelBtn.removeEventListener('click', handleCancel);
                onConfirm();
            };
            
            const handleCancel = () => {
                modal.style.display = 'none';
                confirmBtn.removeEventListener('click', handleConfirm);
                cancelBtn.removeEventListener('click', handleCancel);
            };
            
            confirmBtn.addEventListener('click', handleConfirm);
            cancelBtn.addEventListener('click', handleCancel);
        }

       
        let activeFilterBook = null;

        
        function renderTable() {
            
            updateFinesForReturned();

            let filtered = [...transactions];
            if (activeFilterBook && activeFilterBook !== 'SEMUA') {
                filtered = filtered.filter(t => t.book === activeFilterBook);
            }

            const tbody = document.getElementById('tableBody');
            const jumlahSpan = document.getElementById('jumlahTampil');
            jumlahSpan.innerText = filtered.length;

            if (filtered.length === 0) {
                tbody.innerHTML = `<td><td colspan="8" class="info-empty">Tidak ada transaksi dengan filter ini</td></tr>`;
                return;
            }

            
            tbody.innerHTML = filtered.map((trans, index) => {
                let statusHtml = '';
                if (trans.isReturned) {
                    statusHtml = `<span class="status-badge">✅ Dikembalikan</span>`;
                } else {
                    statusHtml = `<span class="status-badge" style="background:#fff0e6; color:#b45309;">Dipinjam</span>`;
                }

                const fineAmount = trans.fine || 0;
                const dendaText = `Rp ${fineAmount.toLocaleString('id-ID')}`;

                let aksiHtml = '';
                if (!trans.isReturned) {
                    aksiHtml = `<button class="btn-return" data-id="${trans.id}" style="background:#f97316; border:none; color:white; border-radius:30px; padding:0.3rem 0.8rem; font-size:0.7rem; font-weight:500; cursor:pointer;">↩️ Kembalikan</button>`;
                } else {
                    aksiHtml = `<span style="color:#2b6e3b;">✅ Selesai</span>`;
                }

                return `
                <tr>
                    <td><strong>${index + 1}</strong></td>
                    <td style="font-family: monospace;">${trans.code}</td>
                    <td>${trans.member}</td>
                    <td>${trans.book}</td>
                    <td>${trans.borrowDate}</td>
                    <td>${statusHtml}</td>
                    <td class="denda-cell">${dendaText}</td>
                    <td class="aksi-cell">${aksiHtml}</td>
                </tr>`;
            }).join('');

            document.querySelectorAll('.btn-return').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const id = parseInt(btn.getAttribute('data-id'));
                    returnBook(id);
                });
            });
        }

       
        function handleFilterBook() {
            const uniqueBooks = [...new Map(transactions.map(t => [t.book, t.book])).values()];
            if (uniqueBooks.length === 0) {
                activeFilterBook = null;
                renderTable();
                return;
            }
            let msg = "Pilih judul buku untuk filter:\n";
            uniqueBooks.forEach((bk, idx) => {
                msg += `${idx+1}. ${bk}\n`;
            });
            msg += "0. Tampilkan Semua";
            const input = prompt(msg, "0");
            if (input === null) return;
            const choice = parseInt(input);
            if (isNaN(choice)) {
                alert("Masukkan nomor yang valid");
                return;
            }
            if (choice === 0) {
                activeFilterBook = null;
            } else if (choice >= 1 && choice <= uniqueBooks.length) {
                activeFilterBook = uniqueBooks[choice - 1];
            } else {
                alert("Pilihan tidak valid");
                return;
            }
            updateFilterButtonActiveState();
            renderTable();
        }

        function resetFilter() {
            activeFilterBook = null;
            updateFilterButtonActiveState();
            renderTable();
            showToast("Filter telah direset");
        }

        function showAllTransactions() {
            activeFilterBook = null;
            updateFilterButtonActiveState();
            renderTable();
        }

        function updateFilterButtonActiveState() {
            const btnSemua = document.getElementById('filterSemua');
            const btnFilter = document.getElementById('filterBukuBtn');
            const btnReset = document.getElementById('resetFilterBtn');
           
            if (activeFilterBook === null) {
                btnSemua.classList.add('active');
                btnFilter.classList.remove('active');
                btnReset.classList.remove('active');
            } else {
                btnSemua.classList.remove('active');
                btnFilter.classList.add('active');
                btnReset.classList.add('active');
            }
        }

        
        function onPinjamBuku() {
            const anggotaSelect = document.getElementById('selectAnggota');
            const bukuSelect = document.getElementById('selectBuku');
            const anggota = anggotaSelect.value;
            const buku = bukuSelect.value;
            if (!anggota || !buku) {
                alert("Pilih anggota dan buku terlebih dahulu");
                return;
            }
            addTransaction(anggota, buku);
        }

        // Fungsi Navigasi Kembali
        function goBack() {
            showModal("Apakah Anda yakin ingin kembali ke halaman sebelumnya?", () => {
                showToast("🔙 Kembali ke halaman sebelumnya...");
                setTimeout(() => {
                    window.history.back();
                }, 300);
            });
        }

        function goToDashboard() {
            showModal("Apakah Anda yakin ingin kembali ke Dashboard Utama?", () => {
                showToast("Mengarahkan ke Dashboard Utama...");
                setTimeout(() => {
                    // Ganti dengan URL dashboard Anda
                    // window.location.href = "/dashboard";
                    alert("DASHBOARD ADMIN\n\nStatistik Peminjaman: " + transactions.length + " transaksi\nBuku tersedia: 4 judul\nAnggota terdaftar: 5 orang\nTotal denda terkumpul: Rp " + transactions.reduce((sum, t) => sum + t.fine, 0).toLocaleString());
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }, 300);
            });
        }

        
        function init() {
            renderTable();
            const pinjamBtn = document.getElementById('btnPinjamBuku');
            pinjamBtn.addEventListener('click', onPinjamBuku);

            document.getElementById('filterSemua').addEventListener('click', showAllTransactions);
            document.getElementById('filterBukuBtn').addEventListener('click', handleFilterBook);
            document.getElementById('resetFilterBtn').addEventListener('click', resetFilter);
            
            // Event untuk tombol navigasi
            const backBtn = document.getElementById('btnBack');
            const homeBtn = document.getElementById('btnHome');
            
            backBtn.addEventListener('click', goBack);
            homeBtn.addEventListener('click', goToDashboard);
            
            // Menutup modal jika klik di luar
            window.addEventListener('click', (e) => {
                const modal = document.getElementById('confirmModal');
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        init();
    </script>
</body>

</html>