function deleteProduct(delete_path, product_name) {
    var content = 'Do you really want to delete this product [' + product_name + ']. All related data will be delete, too. This process cannot be undone.';
    $('#modalContent').text(content);
    $('#submitDelete').attr('href', delete_path);
    $('#showDialog').click();
}

$(document).ready(function (e) {
    var checkTag = document.getElementById("product_image");
    if (checkTag) {
        //Preview picture before upload
        product_image.onchange = evt => {
            const [file] = product_image.files
            if (file) {
                loadImage.src = URL.createObjectURL(file)
                loadImage.style.display = "block";
            }
        }
    }
});
