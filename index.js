
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

    $('#logout').on('click', function(event) {
        event.preventDefault(); // Ngăn chặn hành động mặc định khi click liên kết

        const confirmLogout = confirm("Bạn có chắc chắn muốn đăng xuất?");
        if (confirmLogout) {
            window.location.href = "admin.php?logout=true"; // Điều hướng đến trang đăng xuất khi xác nhận
        }
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
});

