<?php
$page_title = 'Danh sách Học phần';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Danh sách Học phần</h2>
    <a href="index.php?controller=hocphan&action=create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm Học phần
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Mã HP</th>
                        <th>Tên học phần</th>
                        <th>Số tín chỉ</th>
                        <th>Số lượng SV có thể đăng ký</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($courses) > 0): ?>
                        <?php foreach($courses as $course): ?>
                            <tr>
                                <td><?php echo $course['MaHP']; ?></td>
                                <td><?php echo $course['TenHP']; ?></td>
                                <td><?php echo $course['SoTinChi']; ?></td>
                                <td>
                                    <?php if($course['SoLuongSV'] > 0): ?>
                                        <span class="badge bg-success"><?php echo $course['SoLuongSV']; ?> chỗ trống</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Đã hết chỗ</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?controller=hocphan&action=show&id=<?php echo $course['MaHP']; ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="index.php?controller=hocphan&action=edit&id=<?php echo $course['MaHP']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?controller=hocphan&action=delete&id=<?php echo $course['MaHP']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa học phần này không?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Không có học phần nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?> 