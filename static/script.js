document.addEventListener("DOMContentLoaded", function () {
    // Fetch stocks from the server and render them on the page
    fetch('/stocks')
        .then(response => response.json())
        .then(stocks => renderStocks(stocks))
        .catch(error => console.error('Error fetching stocks:', error));
        

    function renderStocks(stocks) {
        const stocksList = document.getElementById('stocks-list');

        stocks.forEach(stock => {
            const li = document.createElement('li');
            li.textContent = `${stock.id}: ${stock.name} - ${stock.price}`;
            stocksList.appendChild(li);
        });
    }
});
