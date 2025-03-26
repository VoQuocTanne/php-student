<?php
$page_title = 'Chi tiết Đăng ký Học phần';
ob_start();
?>

<h2>Chi tiết Đăng ký Học phần</h2>

<div class="mb-3">
    <a href="javascript:history.back()" class="btn btn-secondary">Quay lại</a>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Danh sách Học phần đã đăng ký</h5>
    </div>
    <div class="card-body">
        <?php if(count($courses) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã học phần</th>
                        <th>Tên học phần</th>
                        <th>Số tín chỉ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalCredits = 0;
                    foreach($courses as $course): 
                        $totalCredits += $course['SoTinChi'];
                    ?>
                        <tr>
                            <td><?php echo $course['MaHP']; ?></td>
                            <td><?php echo $course['TenHP']; ?></td>
                            <td><?php echo $course['SoTinChi']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Tổng số tín chỉ:</th>
                        <th><?php echo $totalCredits; ?></th>
                    </tr>
                </tfoot>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                <p>Không có học phần nào trong đăng ký này.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?> 