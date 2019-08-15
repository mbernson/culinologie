-- Triggers om de recepten telling bij te werken.

DROP TRIGGER IF EXISTS cookbooks_recipes_sum_insert;
DROP TRIGGER IF EXISTS cookbooks_recipes_sum_delete;

delimiter //

CREATE TRIGGER cookbooks_recipes_sum_insert AFTER INSERT ON recipes
FOR EACH ROW
BEGIN

UPDATE cookbooks c SET c.recipes_count = (
	SELECT count(r.id) FROM recipes r WHERE r.cookbook = NEW.cookbook
) WHERE c.slug = NEW.cookbook;

END; //

CREATE TRIGGER cookbooks_recipes_sum_delete AFTER DELETE ON recipes
FOR EACH ROW
BEGIN

UPDATE cookbooks c SET c.recipes_count = (
	SELECT count(r.id) FROM recipes r WHERE r.cookbook = OLD.cookbook
) WHERE c.slug = OLD.cookbook;

END; //

delimiter ;
