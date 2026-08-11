<!-- Modal Auth (Login & Register) -->
<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-white border-secondary">
      <div class="modal-header border-bottom border-secondary">
        <ul class="nav nav-pills card-header-pills" id="pills-tab" role="tablist">
          <li class="nav-item">
            <button class="nav-link active text-white" id="tab-login" data-bs-toggle="pill" data-bs-target="#pills-login" type="button">Login</button>
          </li>
          <li class="nav-item">
            <button class="nav-link text-white" id="tab-register" data-bs-toggle="pill" data-bs-target="#pills-register" type="button">Daftar</button>
          </li>
        </ul>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="tab-content" id="pills-tabContent">
          <!-- FORM LOGIN -->
          <div class="tab-pane fade show active" id="pills-login">
            <form action="proses_auth.php?aksi=login" method="POST">
              <div class="mb-3">
                <label class="form-label text-light">Email</label>
                <input type="email" name="email" class="form-control bg-dark text-white border-secondary" placeholder="email@contoh.com" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-light">Kata Sandi</label>
                <input type="password" name="kata_sandi" class="form-control bg-dark text-white border-secondary" required>
              </div>
              <button type="submit" class="btn btn-primary w-100 fw-bold" style="background: #a855f7; border: none;">Masuk</button>
            </form>
          </div>

          <!-- FORM REGISTER -->
          <div class="tab-pane fade" id="pills-register">
            <form action="proses_auth.php?aksi=register" method="POST">
              <div class="mb-3">
                <label class="form-label text-light">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control bg-dark text-white border-secondary" placeholder="John Doe" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-light">Email</label>
                <input type="email" name="email" class="form-control bg-dark text-white border-secondary" placeholder="email@contoh.com" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-light">Nomor WhatsApp</label>
                <input type="text" name="nomor_telepon" class="form-control bg-dark text-white border-secondary" placeholder="08123456789" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-light">Kata Sandi</label>
                <input type="password" name="kata_sandi" class="form-control bg-dark text-white border-secondary" required>
              </div>
              <button type="submit" class="btn btn-primary w-100 fw-bold" style="background: #a855f7; border: none;">Daftar Akun</button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>