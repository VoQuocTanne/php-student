<?php
$page_title = 'Thêm Sinh viên mới';
ob_start();
?>

<h2>Thêm Sinh viên mới</h2>

<div class="card">
    <div class="card-body">
        <form action="index.php?controller=sinhvien&action=store" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="MaSV" class="form-label">Mã sinh viên</label>
                <input type="text" class="form-control" id="MaSV" name="MaSV" required>
            </div>
            
            <div class="mb-3">
                <label for="HoTen" class="form-label">Họ tên</label>
                <input type="text" class="form-control" id="HoTen" name="HoTen" required>
            </div>
            
            <div class="mb-3">
                <label for="GioiTinh" class="form-label">Giới tính</label>
                <select class="form-select" id="GioiTinh" name="GioiTinh" required>
                    <option value="Nam">Nam</option>
                    <option value="Nữ">Nữ</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="NgaySinh" class="form-label">Ngày sinh</label>
                <input type="date" class="form-control" id="NgaySinh" name="NgaySinh" required>
            </div>
            
            <div class="mb-3">
                <label for="Hinh" class="form-label">Hình ảnh</label>
                <input type="file" class="form-control" id="Hinh" name="Hinh">
            </div>
            
            <div class="mb-3">
                <label for="MaNganh" class="form-label">Ngành học</label>
                <select class="form-select" id="MaNganh" name="MaNganh" required>
                    <?php foreach($majors as $major): ?>
                        <option value="<?php echo $major['MaNganh']; ?>"><?php echo $major['TenNganh']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="index.php?controller=sinhvien&action=index" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?> 