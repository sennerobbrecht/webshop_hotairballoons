document.getElementById('showOrderForm').addEventListener('click', function () {
    document.getElementById('orderForm').style.display = 'flex';
});

function showInsufficientBalancePopup() {
    document.getElementById('insufficientBalancePopup').style.display = 'flex';
}

function closeInsufficientBalancePopup() {
    document.getElementById('insufficientBalancePopup').style.display = 'none';
}

function placeOrder(event) {
   
    var totalAmount = parseFloat(document.getElementById('totalAmount').innerText.replace('€', '').replace(',', '.'));
    var balance = parseFloat(document.getElementById('userBalance').innerText.replace('€', '').replace(',', '.'));

    if (totalAmount > balance) {
    
        showInsufficientBalancePopup();
        return false; 
    }
    
 
    document.getElementById('orderForm').submit();
}




function changeQuantity(button, delta) {
    const input = button.parentElement.querySelector('input[name="quantity"]');
    const current = parseInt(input.value) || 1;
    const newQuantity = Math.max(1, current + delta); 
    input.value = newQuantity;

    
    const priceElement = button.closest('.product-card').querySelector('.product-price');
    const price = parseFloat(priceElement.getAttribute('data-price'));
    const newPrice = (price * newQuantity).toFixed(2);
    priceElement.textContent = '€' + newPrice.replace('.', ',');

   
    updateCartQuantity(input.getAttribute('data-product-id'), newQuantity);
    updateTotalPrice();
}

function updateCartQuantity(productId, quantity) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log('Hoeveelheid bijgewerkt');
        } else {
            console.error('Fout bij bijwerken winkelmand');
        }
    };
    xhr.send('product_id=' + productId + '&quantity=' + quantity);
}

function updateTotalPrice() {
    const priceElements = document.querySelectorAll('.product-price');
    let total = 0;

    priceElements.forEach(function (element) {
        const price = parseFloat(element.getAttribute('data-price'));
        const quantity = parseInt(
            element.closest('.product-card').querySelector('input[name="quantity"]').value
        );
        total += price * quantity;
    });

    document.querySelector('.total-amount').textContent =
        'Totaal: €' + total.toFixed(2).replace('.', ',');
}


