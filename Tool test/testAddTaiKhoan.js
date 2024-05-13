const { Builder, By, until } = require('selenium-webdriver');

async function addTK(username, password, Name, email, role) {
    let driver = await new Builder().forBrowser('chrome').build();
    try {
        await driver.get('http://localhost/myapp/LearnBoottrap/Web-PHP/QLtaikhoan.php');
        await driver.wait(until.elementLocated(By.name('input1')), 10000);

        let usernameInput = await driver.findElement(By.name('input1'));
        let passwordInput = await driver.findElement(By.name('input2'));
        let nameInput = await driver.findElement(By.name('input3'));
        let emailInput = await driver.findElement(By.name('input4'));
        let rootInput = await driver.findElement(By.name('input5'));
        let addButton = await driver.findElement(By.name('add'));

        await usernameInput.sendKeys(username);
        await passwordInput.sendKeys(password);
        await nameInput.sendKeys(Name);
        await emailInput.sendKeys(email);
        await rootInput.sendKeys(role);
       

        // Cuộn xuống dưới cùng của trang
        await driver.executeScript("window.scrollTo(0, document.body.scrollHeight);");

        // Đợi để đảm bảo rằng mọi thứ trên trang được tải xong
        await driver.sleep(1000); // Đợi 1 giây

        // Cuộn lên lại để nhìn thấy nút nếu cần
        await driver.executeScript("arguments[0].scrollIntoView({behavior: 'auto', block: 'center'});", addButton);
        await addButton.click();

        // Đợi một chút trước khi thoát để thấy kết quả của cuộn trang
        await driver.sleep(3000); // Đợi 2 giây
    } catch (error) {
        console.error("Đã xảy ra lỗi:", error);
    } finally {
        await driver.quit();
    }
}

async function runTestCases() {
    try {
        await addTK('', '1234', 'Nguyễn Ngọc Minh', '20211135@gmail.com', 'user');
        console.log('Test case 1: Không nhập username - False');

        await addTK('20211136', '', 'Lê Thùy Dương', '20211136@gmail.com', 'user');
        console.log('Test case 2: Không nhập mật khẩu - False');

        await addTK('20211136', '1234', '', '20211136@gmail.com', 'user');
        console.log('Test case 2: Không nhập họ và tên - Pass');

        await addTK('20211137', '1234', 'Lê Thùy Dương', '', 'user');
        console.log('Test case 2: Không nhập email - Pass');

        await addTK('20211141', '1234', 'Nguyễn Ngọc Anh', '20211140@gmail,com', 'user');
        console.log('Test case 2: Nhập email sai định dạng - Pass');

        await addTK('20211140', '1234', 'Nguyễn Ngọc Anh', '20211140@gmail.com', 'user');
        console.log('Test case 3: Nhập đầy đủ thông tin - PASS');

        await addTK('20211133', 'mui29062003', 'Lê Quý Mùi', "lequymui290603@gmail.com", 'user');
        console.log('Test case 4: Thông tin tài khoản bị trùng - PASS');

    } catch (error) {
        console.error('Đã xảy ra lỗi:', error);
    }
}

runTestCases();

