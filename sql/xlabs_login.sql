-- usuario de bd.
CREATE USER 'jason'@'localhost' IDENTIFIED BY '123456';
CREATE USER 'jason'@'%' IDENTIFIED BY '123456';
GRANT ALL PRIVILEGES ON xlabs.* TO 'jason'@'%' IDENTIFIED BY '123456' WITH GRANT OPTION;
FLUSH PRIVILEGES;
-- rol 
insert into roles(id, nombre) values ('b7ebe888-be81-11e8-a309-54ee75873a76','admin');
-- usuario
ALTER TABLE `xlabs`.`user` 
ADD COLUMN `password` VARCHAR(45) NULL AFTER `name`;
insert into user(id, name, password, tel, active) values('3ca0f04d-be82-11e8-a309-54ee75873a76', 'admin', 'admin', '0000000',1);
-- event
insert into event(id, menuL1, menuL2, url) values('a6622f0c-be83-11e8-a309-54ee75873a76', 'Inicio', 'Inicio','index.html');
-- roles x usuario
CREATE TABLE `rolesXUsuario` (
  `idRol` char(36) NOT NULL,
  `idUsuario` char(36) NOT NULL,
  PRIMARY KEY (`idRol`,`idUsuario`),
  KEY `fk_rolesXUsuario_usuario1_idx` (`idUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- eventos x rol
CREATE TABLE `eventosXRol` (
  `idEvento` char(36) NOT NULL,
  `idRol` char(36) NOT NULL,
  PRIMARY KEY (`idEvento`,`idRol`),
  KEY `fk_eventosXRol_rol1_idx` (`idRol`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- 
insert into eventosXRol values ('a6622f0c-be83-11e8-a309-54ee75873a76','b7ebe888-be81-11e8-a309-54ee75873a76');
insert into rolesXUsuario values ('b7ebe888-be81-11e8-a309-54ee75873a76','3ca0f04d-be82-11e8-a309-54ee75873a76');






