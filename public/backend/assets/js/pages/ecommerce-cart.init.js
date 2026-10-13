document.addEventListener("DOMContentLoaded", function () {
    // Selectores principales
    const cartItems = document.querySelectorAll(".product"); // Cada producto en el carrito
    const orderSummaryTableBody = document.getElementById("order-summary-table").querySelector("tbody");
    const cartTotalElement = document.getElementById("cart-total");
  
    // Función para actualizar el resumen del pedido
    function updateOrderSummary() {
      // Limpiar el contenido actual del resumen
      orderSummaryTableBody.innerHTML = "";
  
      let total = 0;
  
      // Iterar sobre cada producto en el carrito
      cartItems.forEach((item) => {
        const productName = item.querySelector("h5 a").innerText.trim(); // Nombre del producto
        const quantityInput = item.querySelector(".product-quantity"); // Input de cantidad
        const unitPrice = parseFloat(item.querySelector(".product-price").innerText); // Precio unitario
        const quantity = parseInt(quantityInput.value); // Cantidad seleccionada
  
        // Solo mostrar productos con cantidad > 0
        if (quantity > 0) {
          const subtotal = quantity * unitPrice;
  
          // Crear una nueva fila para el resumen
          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${productName}</td>
            <td>${quantity}</td>
            <td>$${unitPrice.toFixed(2)}</td>
            <td>$${subtotal.toFixed(2)}</td>
          `;
          orderSummaryTableBody.appendChild(row);
  
          // Actualizar el total general
          total += subtotal;
        }
      });
  
      // Actualizar el total del carrito
      cartTotalElement.innerText = total.toFixed(2);
    }
  
    // Agregar eventos a los botones de incremento/decremento
    cartItems.forEach((item) => {
      const minusButton = item.querySelector(".minus");
      const plusButton = item.querySelector(".plus");
      const quantityInput = item.querySelector(".product-quantity");
  
      // Función para manejar el incremento
      const handleIncrement = () => {
        const currentValue = parseInt(quantityInput.value);
        quantityInput.value = currentValue + 1;
        updateOrderSummary();
      };
  
      // Función para manejar el decremento
      const handleDecrement = () => {
        const currentValue = parseInt(quantityInput.value);
        if (currentValue > 0) {
          quantityInput.value = currentValue - 1;
          updateOrderSummary();
        }
      };
  
      // Eliminar eventos previos para evitar duplicación
      minusButton.removeEventListener("click", handleDecrement);
      plusButton.removeEventListener("click", handleIncrement);
  
      // Agregar eventos
      minusButton.addEventListener("click", handleDecrement);
      plusButton.addEventListener("click", handleIncrement);
  
      // Evento para cambios directos en el input
      quantityInput.addEventListener("change", () => {
        const currentValue = parseInt(quantityInput.value);
        if (currentValue < 0) {
          quantityInput.value = 0; // Evitar valores negativos
        }
        updateOrderSummary();
      });
    });
  
    // Inicializar el resumen del pedido al cargar la página
    updateOrderSummary();
  });