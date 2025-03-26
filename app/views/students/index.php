<?php
$page_title = 'Danh sách Sinh viên';
ob_start();
?>

<h2>Danh sách Sinh viên</h2>

<div class="mb-3">
    <a href="index.php?controller=sinhvien&action=create" class="btn btn-primary">Thêm Sinh viên</a>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Mã SV</th>
            <th>Hình ảnh</th>
            <th>Họ tên</th>
            <th>Giới tính</th>
            <th>Ngày sinh</th>
            <th>Ngành học</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($students as $student): ?>
            <tr>
                <td><?php echo $student['MaSV']; ?></td>
                <td><?php echo $student['HoTen']; ?></td>
                <td><?php echo $student['GioiTinh']; ?></td>
                <td><?php echo $student['NgaySinh']; ?></td>
                <td style="width: 130px;">
                    <?php if(!empty($student['Hinh'])): ?>
                        <img src="<?php echo $student['Hinh']; ?>" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                    <?php else: ?>
                        <?php 
                            // Use sample images based on student ID
                            $imageFile = ($student['MaSV'] == '0123456789') ? 
                                'app/public/images/sinhvien1.jpeg' : 
                                'app/public/images/sinhvien2.jpeg';
                        ?>
                        <img src="<?php echo $imageFile; ?>" class="img-thumbnail" style="width: 70px; height: 70px; object-fit: cover;">
                    <?php endif; ?>
                </td>
                <td><?php echo $student['TenNganh']; ?></td>
                <td>
                    <a href="index.php?controller=sinhvien&action=show&id=<?php echo $student['MaSV']; ?>" class="btn btn-info btn-sm">Xem</a>
                    <a href="index.php?controller=sinhvien&action=edit&id=<?php echo $student['MaSV']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                    <a href="index.php?controller=sinhvien&action=delete&id=<?php echo $student['MaSV']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?> 