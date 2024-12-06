

function addToCart(productId, title, price, image) {
    $.ajax({
        url: 'addtocard.php',
        method: 'POST',
        data: {
            id: productId,
            title: title,
            price: price,
            image: image
        },
        success: function (response) {
            const result = JSON.parse(response);
            if (result.status === 'success') {
                showPopup('Product toegevoegd aan de winkelwagen');
            } else {
                showPopup('Fout: ' + result.message);
            }
        },
        error: function () {
            showPopup('Er is een fout opgetreden bij het toevoegen aan de winkelmand.');
        }
    });
}

function showPopup(message) {
    const popup = document.getElementById('notification-popup');
    popup.textContent = message;
    popup.classList.remove('hidden');
    popup.classList.add('visible');

    setTimeout(() => {
        popup.classList.remove('visible');
        popup.classList.add('hidden');
    }, 5000);
}