###################################### I N S E R T S I N T O D B  ##########################


  ### U S E R S ###
INSERT INTO users (name, password, email, id_type, active, ip) VALUES 
  ('user1', MD5('1'),'user1@kebaparis.ch','1',TRUE,'255.255.255.255'),
  ('user2', MD5('1'),'user2@kebaparis.ch','1',TRUE,'255.255.255.255'),
  ('user3', MD5('1'),'user3@kebaparis.ch','1',TRUE,'255.255.255.255');

##### T Y P E S ###
INSERT INTO types (name) VALUES ('client'), 
                                ('moderator'),
                                ('admin');

  ### S P O T S ###
INSERT INTO spots (id_creator, name) VALUES 
  ('1','Kebab Einsiedeln'),
  ('2','Kebab Horgen'),
  ('3','Kebab Rapperswil');


  ### R A T I N G S ###
INSERT INTO ratings (id_spot, id_rater, tuerkness, price, taste, location, waittime) VALUES 
  ('1','1','4','4','3','2','2'),
  ('1','2','2','4','5','3','3'),
  ('1','3','3','2','1','4','5'),
  ('2','2','3','4','2','1','5'),
  ('3','3','4','2','4','1','3');


#############################################
############ S T A T I S T I C S  ###########
#############################################


#Average FROM 1x RatingPoint
SELECT AVG(tuerkness) as tuerkness FROM ratings WHERE id_spot = '1'

#Stored Procedure for our statistic filling
// start procedure pstatistic 
DELIMITER //
CREATE PROCEDURE pstatistic(IN id_spot INT)

#SELECT AVG(tuerkness), AVG(price), AVG(taste), AVG(location), AVG(waittime) FROM ratings WHERE id_spot = '1'
SET @count = 
(
  SELECT COUNT(*) FROM statistics WHERE id_spot = @id_spot;
);

# Wenn id_spot existiert in Statistics dann:
IF @count = 1 THEN


#WORKS! not anymore...
UPDATE statistics 
INNER JOIN 
(
SELECT 
  (SELECT COUNT(id) FROM ratings WHERE id_spot = @id_spot) as count
  ,AVG(tuerkness) as avgtuerkness
  ,AVG(price) as avgprice
  ,AVG(taste) as avgtaste
  ,AVG(location) as avglocation
  ,AVG(waittime) as avgwaittime
  ,AVG(tuerkness + price + taste + location + waittime) as avgoverall
  ,id_spot
  FROM ratings
  WHERE id_spot = @id_spot
  ) as r
  SET ratingcount = r.count
  ,tuerkness = r.avgtuerkness
  ,price = r.avgprice
  ,taste = r.avgtaste
  ,location = r.avglocation
  ,waittime = r.avgwaittime
  ,overall = r.avgoverall
  WHERE statistics.id_spot = r.id_spot;

/* C/P without variables

UPDATE statistics 
INNER JOIN 
(
SELECT 
  (SELECT COUNT(id) FROM ratings WHERE id_spot = '1') as count
  ,AVG(tuerkness) as avgtuerkness
  ,AVG(price) as avgprice
  ,AVG(taste) as avgtaste
  ,AVG(location) as avglocation
  ,AVG(waittime) as avgwaittime
  ,AVG(tuerkness + price + taste + location + waittime) as avgoverall
  ,id_spot
  FROM ratings
  WHERE id_spot = '1'
  ) as r
  SET ratingcount = r.count
  ,tuerkness = r.avgtuerkness
  ,price = r.avgprice
  ,taste = r.avgtaste
  ,location = r.avglocation
  ,waittime = r.avgwaittime
  ,overall = r.avgoverall
  WHERE statistics.id_spot = r.id_spot;



  #WHERE statistics.id_spot = @id_spot;
*/



# Sonst neuen Eintrag in Statistic erstellen 
ELSE
  # Funktioniert
  # Erstelle neuen Eintrag in Statistics mit dem Durchschnitt der Bewertungen,ratingcount wo id_spot = 'x'
  INSERT INTO statistics (id_spot
  ,ratingcount
  ,tuerkness
  ,price
  ,taste
  ,location
  ,waittime
  ,overall)
  SELECT id,
    (SELECT COUNT(id) FROM ratings WHERE id_spot = @id_spot)
    ,AVG(tuerkness)
    ,AVG(price)
    ,AVG(taste)
    ,AVG(location)
    ,AVG(waittime)
    ,AVG(tuerkness + price + taste + location + waittime)
  FROM ratings WHERE id_spot = @id_spot;

  # INSERT INTO statistics (id_spot,ratingcount,tuerkness,price,taste,location,waittime,overall) SELECT id,(SELECT COUNT(id) FROM ratings WHERE id_spot = '1') ,AVG(tuerkness), AVG(price), AVG(taste), AVG(location), AVG(waittime), AVG(tuerkness + price + taste + location + waittime) FROM ratings WHERE id_spot = 1;

END IF;
END //
DELIMITER ;
// End procedure pstatistic 


/* Old Crap, will be deleted soon, this is just for our history in git :D
  #UPDATE statistics SET tuerkness= SELECT AVG(tuerkness), AVG(price), AVG(taste), AVG(location), AVG(waittime) FROM ratings WHERE id_spot = @id_spot;
  #Not working (SQL)
  
  UPDATE statistics 
  SET  tuerkness = avgtuerkness
  ,price = avgprice
  ,taste = avgtaste
  FROM 
  (SELECT AVG(tuerkness) as avgtuerkness
  ,AVG(price) as avgprice
  ,AVG(taste) as avgtaste
  ,id_spot
  FROM ratings
  WHERE id_spot = 1
  ) as a
  INNER JOIN statistics s
  ON s.id_spot = a.id_spot
  UPDATE statistics SET tuerkness= (SELECT AVG(tuerkness) FROM ratings WHERE id_spot = @id_spot) WHERE id_spot = @id_spot;
  UPDATE statistics SET (tuerkness,price,taste) (SELECT AVG(tuerkness),AVG(price),AVG(taste) FROM ratings WHERE id_spot = @id_spot)
  UPDATE statistics SET tuerkness= (SELECT AVG(tuerkness) FROM ratings WHERE id_spot = 2) WHERE id_spot = 1
  UPDATE (SELECT AVG(tuerkness), AVG(price), AVG(taste), AVG(location), AVG(waittime) FROM ratings WHERE id_spot = @id_spot) SET tuerkness= ;

  #Insert into Statistics
  #INSERT INTO statistics (tuerkness) VALUES AVG(tuerkness) FROM ratings WHERE id_spot = '1'
  #UPDATE statistics SET tuerkness='(SELECT AVG(tuerkness) FROM ratings WHERE id_spot = '1')' WHERE id_spot= '1';
End */




###################################
############ S P O T S  ###########
###################################

#Rating Count FROM spot
SELECT count(id) FROM ratings WHERE id_spot = '1'

#Overall AVG FROM spots   MAX (25)
SELECT AVG(tuerkness + price + taste + location + waittime) as overall FROM ratings WHERE id_spot = '1'


###################################
########### R A T I N G ###########
###################################

#Check if User has already rated this spot  --> if 0 == NO | < 0 == Yes
SELECT count(id) FROM ratings WHERE id_spot = '1' AND id_rater = '1'

  #User hat noch kein Rating zu spot:
  INSERT INTO ratings (id_spot, id_rater, tuerkness, price, taste, location, waittime) VALUES ('1','1','4','4','3','2','2');

  #User hat schon ein Rating zu spot
  UPDATE ratings SET tuerkness='5',price='5',taste='5' WHERE id_rater = '1' AND id_spot='1';

#Show Users Rating for this spot
SELECT * FROM ratings WHERE id_spot = '1' AND id_rater = '1'

#Gesamte Ratinganzahl von User
SELECT count(id) FROM ratings WHERE id_rater = '1'


#############################################
########### U S E R C O N T R O L ###########
#############################################

# Show email?

# Password change via our classes.php right?

# Delete Account (ok with pw AND user check in one query?)
UPDATE users SET users.active='FALSE' WHERE users.id= '1' AND users.password = MD5('1')
