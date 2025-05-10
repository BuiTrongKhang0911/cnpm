document.getElementById('uploadForm').addEventListener('submit', function (event) {
    event.preventDefault();

    const images = [];
    let valid = true;

    for (let i = 1; i <= 5; i++) {
        const input = document.getElementById(`image${i}`);
        if (input.files.length === 0 && i !== 5) {
            document.getElementById(`error${i}`).textContent = "Không có tệp nào được chọn";
            valid = false;
        } else {
            document.getElementById(`error${i}`).textContent = "";
            const file = input.files[0];
            const path = `assets/image/${file.name}`;
            images.push(path);
        }
    }

    if (valid) {
        console.log('Images to upload:', images);
    }
})

