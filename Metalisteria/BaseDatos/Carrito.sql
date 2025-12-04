CREATE TABLE carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,     -- Quién compra
    producto_id INT NOT NULL,    -- Qué compra
    cantidad INT DEFAULT 1,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    -- Descomenta la siguiente línea si tienes tabla de productos con FK
    -- FOREIGN KEY (producto_id) REFERENCES productos(id)
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);