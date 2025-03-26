<?php
$page_title = 'Danh sách Đăng ký Học phần';
ob_start();
?>

<h2>Danh sách Đăng ký Học phần của: <?php echo $this->sinhVien->HoTen; ?></h2>

<div class="mb-3">
    <a href="index.php?controller=dangky&action=create&student_id=<?php echo $this->sinhVien->MaSV; ?>" class="btn btn-primary">Đăng ký mới</a>
    <a href="index.php?controller=sinhvien&action=show&id=<?php echo $this->sinhVien->MaSV; ?>" class="btn btn-secondary">Quay lại</a>
</div>

<?php 
// Display error messages
if(isset($_GET['error'])) {
    $errorMessage = '';
    
    switch($_GET['error']) {
        case 'delete_course_failed':
            $errorMessage = 'Không thể hủy đăng ký học phần. Vui lòng thử lại.';
            break;
        default:
            $errorMessage = 'Đã xảy ra lỗi. Vui lòng thử lại.';
            break;
    }
    
    echo '<div class="alert alert-danger mb-4">' . $errorMessage . '</div>';
}
?>

<?php if(count($allCourses) > 0): ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tất cả học phần đã đăng ký</h5>
                <div>
                    <span class="badge bg-info rounded-pill">Tổng số học phần: <?php echo $totalCourses; ?></span>
                    <span class="badge bg-warning rounded-pill ms-2">Tổng số tín chỉ: <?php echo $totalCredits; ?></span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Mã học phần</th>
                        <th>Tên học phần</th>
                        <th>Số tín chỉ</th>
                        <th>Ngày đăng ký</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = 1;
                    foreach($allCourses as $course): 
                    ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo $course['MaHP']; ?></td>
                            <td><?php echo $course['TenHP']; ?></td>
                            <td><?php echo $course['SoTinChi']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($course['NgayDK'])); ?></td>
                            <td>
                                <a href="index.php?controller=dangky&action=delete&id=<?php echo $course['MaDK']; ?>&student_id=<?php echo $this->sinhVien->MaSV; ?>&course_only=<?php echo $course['MaHP']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Bạn có chắc chắn muốn hủy đăng ký học phần này không?');">
                                    <i class="fas fa-trash"></i> Hủy đăng ký
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="3" class="text-end">Tổng:</th>
                        <th><?php echo $totalCredits; ?> tín chỉ</th>
                        <th colspan="2"><?php echo $totalCourses; ?> học phần</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <h4 class="mt-5 mb-3">Lịch sử đăng ký:</h4>
    
    <?php foreach($registrationsWithCourses as $registration): ?>
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Đăng ký ngày: <?php echo date('d/m/Y', strtotime($registration['NgayDK'])); ?> (Mã đăng ký: <?php echo $registration['MaDK']; ?>)</h6>
                    <div>
                        <a href="index.php?controller=dangky&action=show&id=<?php echo $registration['MaDK']; ?>" class="btn btn-light btn-sm">Xem chi tiết</a>
                        <a href="index.php?controller=dangky&action=delete&id=<?php echo $registration['MaDK']; ?>&student_id=<?php echo $this->sinhVien->MaSV; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa đăng ký này không? Tất cả các học phần trong đăng ký này sẽ bị hủy.');">Xóa</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">
        <p>Sinh viên chưa có đăng ký học phần nào.</p>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?> 