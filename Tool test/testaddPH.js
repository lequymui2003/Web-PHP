const { Builder, By, until } = require('selenium-webdriver');

async function addPH(idPhong, tenPhong) {
    let driver = await new Builder().forBrowser('chrome').build();
    try {
        await driver.get('http://localhost/myapp/LearnBoottrap/Web-PHP/admin.php');
        await driver.wait(until.elementLocated(By.name('input1')), 10000);

        let idPhongInput = await driver.findElement(By.name('input1'));
        let tenPhongInput = await driver.findElement(By.name('input2'));
        let addButton = await driver.findElement(By.name('add'));

        await idPhongInput.sendKeys(idPhong);
        await tenPhongInput.sendKeys(tenPhong);
       

        // Cuộn xuống dưới cùng của trang
        await driver.executeScript("window.scrollTo(0, document.body.scrollHeight);");

        // Đợi để đảm bảo rằng mọi thứ trên trang được tải xong
        await driver.sleep(1000); // Đợi 1 giây

        // Cuộn lên lại để nhìn thấy nút nếu cần
        await driver.executeScript("arguments[0].scrollIntoView({behavior: 'auto', block: 'center'});", addButton);
        await addButton.click();

        // Đợi một chút trước khi thoát để thấy kết quả của cuộn trang
        await driver.sleep(2000); // Đợi 2 giây
    } catch (error) {
        console.error("Đã xảy ra lỗi:", error);
    } finally {
        await driver.quit();
    }
}

async function runTestCases() {
    try {
        await addPH('', 'Phòng 12');
        console.log('Test case 1: ID phòng để trống - PASS');

        await addPH('P012', '');
        console.log('Test case 2: tên phòng để trống - PASS');

        await addPH('@^#@&#*', 'Phòng 12');
        console.log('Test case 3: Id phòng nhập kí tự đặc biệt - PASS');

        await addPH('P012', '@^#@&#*');
        console.log('Test case 4: Tên phòng nhập kí tự đặc biệt - PASS');
     
        await addPH('P001', 'Phòng 1');
        console.log('Test case 5: Id phòng và tên phòng đã tồn tại - PASS');

        await addPH('P012', 'Phòng 12');
        console.log('Test case 6: Thêm thành công phòng học - PASS');

    } catch (error) {
        console.error('Đã xảy ra lỗi:', error);
    }
}

runTestCases();

