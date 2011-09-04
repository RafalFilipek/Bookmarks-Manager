CREATE TABLE `Category` (
   `id` int(11) not null auto_increment,
   `name` varchar(50),
   `marked` tinyint(1) default '0',
   PRIMARY KEY (`id`)
);
