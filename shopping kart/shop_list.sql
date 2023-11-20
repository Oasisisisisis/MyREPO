CREATE TABLE product (
    'id' INT NOT NULL AUTO_INCREMENT,
    'name' VARCHAR(255) NOT NULL,
    'price' INT NOT NULL,
    'remain' INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
);

INSERT INTO `product` (`id`, `name`, `price`, `remain`) VALUES (NULL, 'coffee', '420', '76');