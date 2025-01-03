import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["status"];

    changeStatus(event) {
        const ticketId = event.currentTarget.dataset.id;
        const newStatus = event.currentTarget.value;

        fetch(`/admin/tickets/${ticketId}/status`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ status: newStatus }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Simulación del envío de mensaje
                    fetch("/admin/notification/send", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            email: data.userEmail,
                            subject: "Actualización de tu Ticket",
                            body: `El estado de tu ticket ha cambiado a "${data.ticketStatus}".`,
                        }),
                    });
                    alert(`Estado de ticket actualizado a: ${data.ticketStatus} y enviado via mail al cliente`);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Error al actualizar el estado.");
            });
    }
}