const mercureURL = "http://localhost:3000/.well-known/mercure";
const topic = "tickets"; // El topic para recibir las actualizaciones

// Crear una conexión con el hub de Mercure
const eventSource = new EventSource(mercureURL + "?topic=" + encodeURIComponent(topic), {
    withCredentials: true
});

// Manejar los mensajes recibidos
eventSource.onmessage = function(event) {
    const data = JSON.parse(event.data);

    // Aquí puedes actualizar el DOM para mostrar el nuevo ticket
    const ticketList = document.getElementById("tickets-list");
    const newTicketElement = document.createElement("div");
    // newTicketElement.setAttribute("class", "alert alert-success alert-dismissible");
    let message = "Nuevo ticket -> ID " + data.id + " (Nombre: " + data.name + " - Prioridad: " + data.urgency + " - Tipo problema: " + data.problem_type + ")";
    newTicketElement.innerHTML = [
        `<div class="alert alert-dark alert-dismissible" role="alert">`,
        `   <div>${message}</div>`,
        '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
        '</div>'
    ].join('')
    // newTicketElement.innerText = "Nuevo ticket -> ID " + data.id + " (Nombre: " + data.name + " - Prioridad: " + data.urgency + " - Tipo problema: " + data.problem_type + ")";
    ticketList.appendChild(newTicketElement);
};