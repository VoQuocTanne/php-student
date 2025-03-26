<?php
$page_title = 'Đăng ký Học phần';
ob_start();

// Create a lookup array of already registered courses for easy checking
$registeredCoursesLookup = array();
if(isset($registeredCourses) && is_array($registeredCourses)) {
    foreach($registeredCourses as $rc) {
        $registeredCoursesLookup[$rc['MaHP']] = $rc['TenHP'];
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Đăng ký Học phần cho sinh viên: <?php echo $this->sinhVien->HoTen; ?></h2>
    <a href="index.php?controller=sinhvien&action=show&id=<?php echo $this->sinhVien->MaSV; ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<?php if(isset($error)): ?>
    <div class="alert alert-danger mb-4">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="index.php?controller=dangky&action=store" method="post">
            <input type="hidden" name="MaSV" value="<?php echo $this->sinhVien->MaSV; ?>">
            
            <div class="mb-3">
                <p class="fw-bold mb-3">Chọn các học phần bạn muốn đăng ký:</p>
                
                <div class="row">
                    <?php foreach($courses as $course): ?>
                        <?php 
                        $isRegistered = isset($registeredCoursesLookup[$course['MaHP']]);
                        $isFull = $course['SoLuongSV'] <= 0;
                        $cardClass = '';
                        $disabled = '';
                        $statusBadge = '';
                        
                        if($isRegistered) {
                            $cardClass = 'border-info';
                            $disabled = 'disabled';
                            $statusBadge = '<span class="badge bg-info">Đã đăng ký</span>';
                        } elseif($isFull) {
                            $cardClass = 'border-danger';
                            $disabled = 'disabled';
                            $statusBadge = '<span class="badge bg-danger">Đã hết chỗ</span>';
                        } else {
                            $statusBadge = '<span class="badge bg-success">' . $course['SoLuongSV'] . ' chỗ trống</span>';
                        }
                        ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 <?php echo $cardClass; ?>">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="courses[]" 
                                               value="<?php echo $course['MaHP']; ?>" 
                                               id="course_<?php echo $course['MaHP']; ?>"
                                               <?php echo $disabled; ?>>
                                        <label class="form-check-label fw-bold" for="course_<?php echo $course['MaHP']; ?>">
                                            <?php echo $course['MaHP']; ?> - <?php echo $course['TenHP']; ?>
                                        </label>
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge bg-info text-dark"><?php echo $course['SoTinChi']; ?> tín chỉ</span>
                                        <?php echo $statusBadge; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Đăng ký</button>
                <a href="index.php?controller=sinhvien&action=show&id=<?php echo $this->sinhVien->MaSV; ?>" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?> 