<?php
$page_title = 'Chỉnh sửa Sinh viên';
ob_start();
?>

<h2>Chỉnh sửa Sinh viên</h2>

<form action="index.php?controller=sinhvien&action=update" method="post" enctype="multipart/form-data">
    <input type="hidden" name="MaSV" value="<?php echo $this->sinhVien->MaSV; ?>">
    
    <div class="mb-3">
        <label for="HoTen" class="form-label">Họ tên</label>
        <input type="text" class="form-control" id="HoTen" name="HoTen" value="<?php echo $this->sinhVien->HoTen; ?>" required>
    </div>
    
    <div class="mb-3">
        <label for="GioiTinh" class="form-label">Giới tính</label>
        <select class="form-select" id="GioiTinh" name="GioiTinh" required>
            <option value="Nam" <?php echo ($this->sinhVien->GioiTinh == 'Nam') ? 'selected' : ''; ?>>Nam</option>
            <option value="Nữ" <?php echo ($this->sinhVien->GioiTinh == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label for="NgaySinh" class="form-label">Ngày sinh</label>
        <input type="date" class="form-control" id="NgaySinh" name="NgaySinh" value="<?php echo $this->sinhVien->NgaySinh; ?>" required>
    </div>
    
    <div class="mb-3">
        <label for="MaNganh" class="form-label">Ngành học</label>
        <select class="form-select" id="MaNganh" name="MaNganh" required>
            <?php foreach($majors as $major): ?>
                <option value="<?php echo $major['MaNganh']; ?>" <?php echo ($this->sinhVien->MaNganh == $major['MaNganh']) ? 'selected' : ''; ?>>
                    <?php echo $major['TenNganh']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="mb-3">
        <label for="Hinh" class="form-label">Hình ảnh</label>
        <input type="file" class="form-control" id="Hinh" name="Hinh">
        <small class="text-muted">Để trống nếu không muốn thay đổi hình ảnh hiện tại.</small>
    </div>
    
    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="index.php?controller=sinhvien&action=index" class="btn btn-secondary">Hủy</a>
    </div>
</form>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?> 