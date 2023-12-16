
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

    // //hiển thị form quên mật khẩu
    // $('#forgotPassword').on('click', function(event) {
    //     event.preventDefault();
    //     $('#login').hide();
    //     $('#form-forgotPassword').removeClass("d-none").addClass("d-block");
    // });
    
    // //random mã ngẫu nhiên và kiểm tra form quên mật khẩu
    //     // Mã ngẫu nhiên ban đầu
    //     let currentCode = generateRandomCode();
    //     $('#random-code').text(currentCode);
    
    //     // Hàm sinh mã ngẫu nhiên
    //     function generateRandomCode() {
    //         const codeLength = 5;
    //         const characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    //         let code = '';
    //         for (let i = 0; i < codeLength; i++) {
    //             const randomIndex = Math.floor(Math.random() * characters.length);
    //             code += characters.charAt(randomIndex);
    //         }
    //         return code;
    //     }
    //     // Xử lý sự kiện khi submit form
    //     $('#form-forgotPassword').on('submit', function(event) {
    //         event.preventDefault();
    //         var inputCode = $('#input-code').val();
    //         // var forgotUsernameValue = $("#forgotUsername").val();
    //         // var emailValue = $("#email").val();

    //         // if(forgotUsernameValue === "" && emailValue === ""){
    //         //     alert("Mời bạn nhập tên đăng nhập và email");
    //         // }
    //         // else if(forgotUsernameValue === ""){
    //         //     alert("Mời bạn nhập tên đăng nhập");
    //         // }
    //         // else if(emailValue === ""){
    //         //     alert("Mời bạn nhập email");
    //         // }
    //          if(inputCode === ""){
    //             alert("Mời bạn nhập mã");
    //         } else {
    //             alert('Mã nhập sai! Vui lòng nhập lại.');
    //             // Tạo mã mới và cập nhật hiển thị
    //             currentCode = generateRandomCode();
    //             $('#random-code').text(currentCode);
    //         }
    //     });

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

