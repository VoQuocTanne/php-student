<?php
$page_title = 'Chỉnh sửa Học phần';
ob_start();
?>

<h2>Chỉnh sửa Học phần</h2>

<form action="index.php?controller=hocphan&action=update" method="post">
    <input type="hidden" name="MaHP" value="<?php echo $this->hocPhan->MaHP; ?>">
    
    <div class="mb-3">
        <label for="MaHP" class="form-label">Mã học phần</label>
        <input type="text" class="form-control" id="MaHP" value="<?php echo $this->hocPhan->MaHP; ?>" disabled>
    </div>
    
    <div class="mb-3">
        <label for="TenHP" class="form-label">Tên học phần</label>
        <input type="text" class="form-control" id="TenHP" name="TenHP" value="<?php echo $this->hocPhan->TenHP; ?>" required>
    </div>
    
    <div class="mb-3">
        <label for="SoTinChi" class="form-label">Số tín chỉ</label>
        <input type="number" class="form-control" id="SoTinChi" name="SoTinChi" value="<?php echo $this->hocPhan->SoTinChi; ?>" min="1" max="10" required>
    </div>
    
    <div class="mb-3">
        <label for="SoLuongSV" class="form-label">Số lượng sinh viên tối đa</label>
        <input type="number" class="form-control" id="SoLuongSV" name="SoLuongSV" value="<?php echo $this->hocPhan->SoLuongSV; ?>" min="0" required>
        <small class="text-muted">Số lượng sinh viên còn được phép đăng ký học phần này</small>
    </div>
    
    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="index.php?controller=hocphan&action=index" class="btn btn-secondary">Hủy</a>
    </div>
</form>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?> 