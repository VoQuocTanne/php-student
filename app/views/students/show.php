<?php
$page_title = 'Thông tin Sinh viên';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Thông tin Sinh viên</h2>
    <div>
        <a href="index.php?controller=sinhvien&action=edit&id=<?php echo $this->sinhVien->MaSV; ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Chỉnh sửa
        </a>
        <a href="index.php?controller=sinhvien&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <?php if($this->sinhVien->Hinh): ?>
                    <img src="<?php echo $this->sinhVien->Hinh; ?>" class="img-fluid rounded mb-3" style="max-height: 250px;">
                <?php else: ?>
                    <img src="app/public/images/no-image.jpg" class="img-fluid rounded mb-3" style="max-height: 250px;">
                <?php endif; ?>
                <h4><?php echo $this->sinhVien->HoTen; ?></h4>
                <p class="text-muted"><?php echo $this->sinhVien->MaSV; ?></p>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Đăng ký học phần</h5>
            </div>
            <div class="card-body">
                <a href="index.php?controller=dangky&action=create&student_id=<?php echo $this->sinhVien->MaSV; ?>" class="btn btn-success w-100">
                    <i class="fas fa-book"></i> Đăng ký học phần
                </a>
                <a href="index.php?controller=dangky&action=index&student_id=<?php echo $this->sinhVien->MaSV; ?>" class="btn btn-info w-100 mt-2">
                    <i class="fas fa-list"></i> Xem học phần đã đăng ký
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Thông tin chi tiết</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 30%;">Mã sinh viên:</th>
                        <td><?php echo $this->sinhVien->MaSV; ?></td>
                    </tr>
                    <tr>
                        <th>Họ tên:</th>
                        <td><?php echo $this->sinhVien->HoTen; ?></td>
                    </tr>
                    <tr>
                        <th>Giới tính:</th>
                        <td><?php echo $this->sinhVien->GioiTinh; ?></td>
                    </tr>
                    <tr>
                        <th>Ngày sinh:</th>
                        <td><?php echo date('d/m/Y', strtotime($this->sinhVien->NgaySinh)); ?></td>
                    </tr>
                    <tr>
                        <th>Ngành học:</th>
                        <td><?php echo $this->nganhHoc->TenNganh; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?> 