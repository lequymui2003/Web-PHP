
$(document).ready(function(){
    //hiển thị mật khẩu
    $('.toggle-password').on('click', function(){
         var passwordField = $("#password");
         var fieldType = passwordField.attr('type');
         if (fieldType === 'password') {
            passwordField.attr('type', 'text');
            $(this).removeClass('bi bi-eye').addClass('bi bi-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            $(this).removeClass('bi bi-eye-slash').addClass('bi bi-eye');
        }
    });
    
    // lấy ra giá trị của bảng vào các ô input
    $("#table tbody tr").click(function() {
        var input1 = $(this).find("td:eq(0)").text().trim();
        var input2 = $(this).find("td:eq(1)").text().trim();
        var input3 = $(this).find("td:eq(2)").text().trim();
        var input4 = $(this).find("td:eq(3)").text().trim();
        var input5 = $(this).find("td:eq(4)").text().trim();
        var input6 = $(this).find("td:eq(5)").text().trim();
        var input7 = $(this).find("td:eq(6)").text().trim();

        $('input[name="input1"]').val(input1); 
        $('input[name="input2"]').val(input2); 
        $('input[name="input3"]').val(input3); 
        $('input[name="input4"]').val(input4); 
        $('input[name="input5"]').val(input5); 
        $('input[name="input6"]').val(input6); 
        $('input[name="input7"]').val(input7); 
    });

    // nút đăng xuất
    $('#logout').on('click', function(event) {
        event.preventDefault();
    
        Swal.fire({
            title: 'Xác nhận đăng xuất',
            text: 'Bạn có chắc chắn muốn đăng xuất?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "admin.php?logout=true";
                window.location.href = "user.php?logout=true";
                // Hoặc window.location.href = "user.php?logout=true"; nếu cần
            }
        });
    });

        // trang chủ
        $("#icon-user").on('click', function(event) {
            event.preventDefault();
            $("#sub-nav").toggleClass("d-none d-block");
        });
    
        // Khi click vào bất kỳ nơi nào trên trang
        $(document).click(function(event) {
            // Nếu sự kiện click không nằm trong dropdown menu hoặc biểu tượng người dùng, ẩn nó đi
            if (!$(event.target).closest('#sub-nav, #icon-user').length) {
                $('#sub-nav').removeClass('d-block').addClass('d-none');
            }
        });


        $('#changePasswordForm').on('submit', function(event) {
            event.preventDefault(); // Ngăn chặn submit mặc định của form
        
            // Lấy giá trị từ các trường input
            const oldPassword = $('#oldPassword').val();
            const newPassword = $('#newPassword').val();
            const confirmPassword = $('#confirmPassword').val();
          
            // Kiểm tra các điều kiện của mật khẩu và xử lý logic ở đây
            let error = "";
            let success = "";
        
            if (oldPassword === "" || newPassword === "" || confirmPassword === "") {
                error = "Mời bạn điền đầy đủ thông tin";
            } else if(newPassword !== confirmPassword) {
                error = "Xác nhận mật khẩu không khớp, mời nhập lại";
            } else {
                // Xử lý logic đổi mật khẩu ở đây
                $.ajax({
                    type: "POST",
                    url: "process_change_password.php", // Đường dẫn đến file xử lý PHP
                    data: {
                        oldPassword: oldPassword,
                        newPassword: newPassword,
                        confirmPassword: confirmPassword
                    },
                    success: function(response) {
                        if (response === "success") {
                            $('#messageContainer').html('<p class="success-message">Đổi mật khẩu thành công</p>');
                        } else {
                            $('#messageContainer').html('<p class="error-message">' + response + '</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#messageContainer').html('<p class="error-message">Lỗi không xác định. Vui lòng thử lại sau.</p>');
                    }
                });
            }
        });
        
});

