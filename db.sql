CREATE TABLE `Category` (
   `id` int(11) not null auto_increment,
   `name` varchar(50),
   `marked` tinyint(1) default '0',
   PRIMARY KEY (`id`)
);

CREATE TABLE `Bookmark` (
   `id` int(11) not null auto_increment,
   `href` varchar(255) not null,
   PRIMARY KEY (`id`)
)

CREATE TABLE `bookmark_category` (
   `bookmark_id` int(11) not null,
   `category_id` int(11) not null,
   KEY `bookmark_id` (`bookmark_id`,`category_id`)
);