<?php
$page_title = 'Thêm Học phần mới';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Thêm Học phần mới</h2>
    <a href="index.php?controller=hocphan&action=index" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="index.php?controller=hocphan&action=store" method="post">
            <div class="mb-3">
                <label for="MaHP" class="form-label">Mã học phần</label>
                <input type="text" class="form-control" id="MaHP" name="MaHP" maxlength="10" required>
                <small class="text-muted">Tối đa 10 ký tự</small>
            </div>
            
            <div class="mb-3">
                <label for="TenHP" class="form-label">Tên học phần</label>
                <input type="text" class="form-control" id="TenHP" name="TenHP" required>
            </div>
            
            <div class="mb-3">
                <label for="SoTinChi" class="form-label">Số tín chỉ</label>
                <input type="number" class="form-control" id="SoTinChi" name="SoTinChi" min="1" max="10" required>
            </div>
            
            <div class="mb-3">
                <label for="SoLuongSV" class="form-label">Số lượng sinh viên tối đa</label>
                <input type="number" class="form-control" id="SoLuongSV" name="SoLuongSV" min="1" value="50" required>
                <small class="text-muted">Số lượng sinh viên được phép đăng ký học phần này</small>
            </div>
            
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="index.php?controller=hocphan&action=index" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?> 