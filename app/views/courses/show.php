<?php
$page_title = 'Thông tin Học phần';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Thông tin Học phần</h2>
    <div>
        <a href="index.php?controller=hocphan&action=edit&id=<?php echo $this->hocPhan->MaHP; ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Chỉnh sửa
        </a>
        <a href="index.php?controller=hocphan&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Thông tin chi tiết</h5>
    </div>
    <div class="card-body">
        <table class="table">
            <tr>
                <th style="width: 30%;">Mã học phần:</th>
                <td><?php echo $this->hocPhan->MaHP; ?></td>
            </tr>
            <tr>
                <th>Tên học phần:</th>
                <td><?php echo $this->hocPhan->TenHP; ?></td>
            </tr>
            <tr>
                <th>Số tín chỉ:</th>
                <td><?php echo $this->hocPhan->SoTinChi; ?></td>
            </tr>
            <tr>
                <th>Số lượng sinh viên có thể đăng ký:</th>
                <td>
                    <?php if($this->hocPhan->SoLuongSV > 0): ?>
                        <span class="badge bg-success"><?php echo $this->hocPhan->SoLuongSV; ?> chỗ trống</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Đã hết chỗ</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?> 