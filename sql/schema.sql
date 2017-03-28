DROP TABLE IF EXISTS dominantedgegrid8;
DROP TABLE IF EXISTS dominantedgegrid16;
DROP TABLE IF EXISTS ehd;
DROP TABLE IF EXISTS edgegrid16full;
DROP TABLE IF EXISTS edgearp88full;
DROP TABLE IF EXISTS edgegrid16;
DROP TABLE IF EXISTS edgearp88;
DROP TABLE IF EXISTS iph_met;
DROP TABLE IF EXISTS wmk_met;
DROP TABLE IF EXISTS pic_met;
DROP TABLE IF EXISTS picture;
DROP TABLE IF EXISTS iph_wmk;
DROP TABLE IF EXISTS watermark;
DROP TABLE IF EXISTS iph_iph;
DROP TABLE IF EXISTS IPH_Type;
DROP TABLE IF EXISTS Metadata_type;
DROP TABLE IF EXISTS usr_uro_reg;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS ResearchGroup;
DROP TABLE IF EXISTS UserRole;
DROP TABLE IF EXISTS testtable;

CREATE TABLE testtable(
id serial primary key,
testcolumn1 text,
testcolumn2 int
);

CREATE TABLE UserRole(
uro_id serial PRIMARY KEY,
uro_name text,
uro_addUser boolean,
uro_editUser boolean,
uro_deleteUser boolean,
uro_createResearchGroup boolean,
uro_editResearchGroup boolean,
uro_deleteResearchGroup boolean,
uro_editOwnResearchGroup boolean,
uro_deleteOwnResearchGroup boolean,
uro_uploadNewWatermark boolean,
uro_uploadNewIphType boolean,
uro_editAllWatermarks boolean,
uro_editWaterMarksOfResearchGroup boolean,
uro_editAllIphTypes boolean,
uro_editIphTypesOfResearchGroup boolean
);

CREATE INDEX userrole_idx
  ON UserRole
  USING btree
  (uro_id);


CREATE TABLE ResearchGroup(
reg_id serial PRIMARY KEY,
reg_name text
);

CREATE INDEX ResearchGroup_idx
  ON ResearchGroup
  USING btree
  (reg_id);


CREATE TABLE Users(
usr_id serial PRIMARY KEY,
usr_username text,
usr_email text UNIQUE,
usr_pw_hash text,
usr_pw_salt text,
usr_validation_status boolean,
usr_validation_hash text
);

CREATE INDEX Users_idx
  ON Users
  USING btree
  (usr_id);


CREATE TABLE usr_uro_reg(
uur_usr_id int NOT NULL,
uur_uro_id int NOT NULL,
uur_reg_id int NULL,

CONSTRAINT uur_usr_fkey FOREIGN KEY (uur_usr_id) REFERENCES Users(usr_id) ON DELETE CASCADE,
CONSTRAINT uur_uro_fkey FOREIGN KEY (uur_uro_id) REFERENCES UserRole(uro_id) ON DELETE CASCADE,
CONSTRAINT uur_reg_fkey FOREIGN KEY (uur_reg_id) REFERENCES ResearchGroup(reg_id) ON DELETE CASCADE,
CONSTRAINT uur_pkey PRIMARY KEY (uur_usr_id, uur_reg_id)
);

CREATE TABLE Metadata_type(
met_id serial PRIMARY KEY,
met_label text,
met_url text
);

CREATE INDEX Metadata_type_idx
  ON Metadata_type
  USING btree
  (met_id);


CREATE TABLE IPH_Type(
iph_id serial PRIMARY KEY,
iph_nr text,
iph_name text,
iph_url text,
iph_usr_id int,
iph_reg_id int DEFAULT 1 ,
CONSTRAINT iph_usr_fkey FOREIGN KEY (iph_usr_id) REFERENCES Users(usr_id) ON DELETE SET NULL,
CONSTRAINT iph_reg_fkey FOREIGN KEY (iph_reg_id) REFERENCES ResearchGroup(reg_id) ON DELETE SET DEFAULT
);

CREATE INDEX IPH_Type_idx
  ON IPH_Type
  USING btree
  (iph_id);


CREATE TABLE iph_iph(
iix_subtype_iph_id int,
iix_supertype_iph_id int,
CONSTRAINT iix_iph_sub_fkey FOREIGN KEY (iix_subtype_iph_id) REFERENCES IPH_Type(iph_id) ON DELETE CASCADE,
CONSTRAINT iix_iph_sup_fkey FOREIGN KEY (iix_supertype_iph_id) REFERENCES IPH_Type(iph_id) ON DELETE CASCADE,
CONSTRAINT iix_pkey PRIMARY KEY (iix_subtype_iph_id, iix_supertype_iph_id)
);


CREATE TABLE watermark(
wmk_id serial PRIMARY KEY,
wmk_reference_number text,
wmk_name text,
wmk_usr_id int,
wmk_reg_id int DEFAULT 1 ,
CONSTRAINT wmk_usr_fkey FOREIGN KEY (wmk_usr_id) REFERENCES Users(usr_id) ON DELETE SET NULL,
CONSTRAINT wmk_reg_fkey FOREIGN KEY (wmk_reg_id) REFERENCES ResearchGroup(reg_id) ON DELETE SET DEFAULT
);

CREATE INDEX watermark_idx
  ON watermark
  USING btree
  (wmk_id);



CREATE TABLE wmk_wmk(
wwx_wmk_consists_of_id int NOT NULL,
wwx_wmk_id int NOT NULL,

CONSTRAINT wwx_wmk_consists_fkey FOREIGN KEY (wwx_wmk_consists_of_id) REFERENCES Watermark(wmk_id) ON DELETE CASCADE,
CONSTRAINT wwx_wmk_id_fkey FOREIGN KEY (wwx_wmk_id) REFERENCES  Watermark(wmk_id) ON DELETE CASCADE,
CONSTRAINT wwx_pkey PRIMARY KEY (wwx_wmk_id, wwx_wmk_consist_of_id)
);


CREATE TABLE iph_wmk(
iwx_wmk_id int NOT NULL,
iwx_iph_id int NOT NULL,

CONSTRAINT iwx_wmk_fkey FOREIGN KEY (iwx_wmk_id) REFERENCES Watermark(wmk_id) ON DELETE CASCADE,
CONSTRAINT iwx_iph_fkey FOREIGN KEY (iwx_iph_id) REFERENCES IPH_Type(iph_id) ON DELETE CASCADE,
CONSTRAINT iwx_pkey PRIMARY KEY (iwx_wmk_id, iwx_iph_id)
);


CREATE TABLE picture(
pic_id serial PRIMARY KEY,
pic_sketch boolean,
pic_path text UNIQUE NOT NULL,
pic_md5 text,
pic_wmk_id int,
pic_iph_id int,
pic_usr_id int,
pic_reg_id int DEFAULT 1,
CONSTRAINT pic_usr_fkey FOREIGN KEY (pic_usr_id) REFERENCES Users(usr_id) ON DELETE SET NULL,
CONSTRAINT pic_reg_fkey FOREIGN KEY (pic_reg_id) REFERENCES ResearchGroup(reg_id) ON DELETE SET DEFAULT,
CONSTRAINT pic_wmk_fkey FOREIGN KEY (pic_wmk_id) REFERENCES watermark(wmk_id) ON DELETE CASCADE,
CONSTRAINT pic_iph_fkey FOREIGN KEY (pic_iph_id) REFERENCES IPH_Type(iph_id) ON DELETE CASCADE
);

CREATE INDEX picture_idx
  ON picture
  USING btree
  (pic_id);
  
CREATE INDEX picture_wmk_idx
  ON picture
  USING btree
  (pic_wmk_id);
  
CREATE INDEX picture_iph_idx
  ON picture
  USING btree
  (pic_iph_id);



CREATE TABLE pic_met(
pmx_pic_id int,
pmx_met_id int,
pmx_value text,
pmx_reg_id int,
CONSTRAINT pmx_pic_fkey FOREIGN KEY (pmx_pic_id) REFERENCES picture(pic_id) ON DELETE CASCADE,
CONSTRAINT pmx_met_fkey FOREIGN KEY (pmx_met_id) REFERENCES Metadata_type(met_id) ON DELETE CASCADE,
CONSTRAINT pmx_reg_fkey FOREIGN KEY (pmx_reg_id) REFERENCES ResearchGroup(reg_id) ON DELETE SET NULL,
CONSTRAINT pmx_pkey PRIMARY KEY (pmx_pic_id, pmx_met_id, pmx_value)
);


CREATE TABLE wmk_met(
wmx_wmk_id int,
wmx_met_id int,
wmx_value text,
wmx_reg_id int,
CONSTRAINT wmx_wmk_fkey FOREIGN KEY (wmx_wmk_id) REFERENCES watermark(wmk_id) ON DELETE CASCADE,
CONSTRAINT wmx_met_fkey FOREIGN KEY (wmx_met_id) REFERENCES Metadata_type(met_id) ON DELETE CASCADE,
CONSTRAINT wmx_reg_fkey FOREIGN KEY (wmx_reg_id) REFERENCES ResearchGroup(reg_id) ON DELETE SET NULL,
CONSTRAINT wmx_pkey PRIMARY KEY (wmx_wmk_id, wmx_met_id, wmx_value)
);

CREATE TABLE iph_met(
imx_iph_id int,
imx_met_id int,
imx_value text,
imx_reg_id int,
CONSTRAINT imx_iph_fkey FOREIGN KEY (imx_iph_id) REFERENCES IPH_Type(iph_id) ON DELETE CASCADE,
CONSTRAINT imx_met_fkey FOREIGN KEY (imx_met_id) REFERENCES Metadata_type(met_id) ON DELETE CASCADE,
CONSTRAINT imx_reg_fkey FOREIGN KEY (imx_reg_id) REFERENCES ResearchGroup(reg_id) ON DELETE SET NULL,
CONSTRAINT imx_pkey PRIMARY KEY (imx_iph_id, imx_met_id, imx_value)
);



CREATE TABLE edgearp88
(
  fet_pic_id bigint NOT NULL,
  arp feature,
  CONSTRAINT edgearp88_pkey PRIMARY KEY (fet_pic_id),
  CONSTRAINT edgearp88_fet_pic_id_fkey FOREIGN KEY (fet_pic_id)
      REFERENCES picture(pic_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
CREATE INDEX edgearp88_fet_pic_id_idx
  ON edgearp88
  USING btree
  (fet_pic_id);
  
  
  
CREATE TABLE edgegrid16
(
  fet_pic_id bigint NOT NULL,
  grid feature,
  CONSTRAINT edgegrid16_pkey PRIMARY KEY (fet_pic_id),
  CONSTRAINT edgegrid16_fet_pic_id_fkey FOREIGN KEY (fet_pic_id)
      REFERENCES picture(pic_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE INDEX edgegrid16_fet_pic_id_idx
  ON edgegrid16
  USING btree
  (fet_pic_id);  
  
  
  
CREATE TABLE edgearp88full
(
  fet_pic_id bigint NOT NULL,
  arp feature,
  CONSTRAINT edgearp88full_pkey PRIMARY KEY (fet_pic_id),
  CONSTRAINT edgearp88full_fet_pic_id_fkey FOREIGN KEY (fet_pic_id)
      REFERENCES picture(pic_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE INDEX edgearp88full_fet_pic_id_idx
  ON edgearp88full
  USING btree
  (fet_pic_id);


CREATE TABLE edgegrid16full
(
  fet_pic_id bigint NOT NULL,
  grid feature,
  CONSTRAINT edgegrid16full_pkey PRIMARY KEY (fet_pic_id),
  CONSTRAINT edgegrid16full_fet_pic_id_fkey FOREIGN KEY (fet_pic_id)
      REFERENCES picture(pic_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX edgegrid16full_fet_pic_id_idx
  ON edgegrid16full
  USING btree
  (fet_pic_id);


CREATE TABLE ehd
(
  fet_pic_id bigint NOT NULL,
  hist feature,
  CONSTRAINT ehd_pkey PRIMARY KEY (fet_pic_id),
  CONSTRAINT ehd_fet_pic_id_fkey FOREIGN KEY (fet_pic_id)
      REFERENCES picture(pic_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE INDEX ehd_fet_pic_id_idx
  ON ehd
  USING btree
  (fet_pic_id);


CREATE TABLE dominantedgegrid16
(
  fet_pic_id bigint NOT NULL,
  edges feature,
  CONSTRAINT dominantedgegrid16_pkey PRIMARY KEY (fet_pic_id),
  CONSTRAINT dominantedgegrid16_fet_pic_id_fkey FOREIGN KEY (fet_pic_id)
      REFERENCES picture(pic_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX dominantedgegrid16_fet_pic_id_idx
  ON dominantedgegrid16
  USING btree
  (fet_pic_id);


CREATE TABLE dominantedgegrid8
(
  fet_pic_id bigint NOT NULL,
  edges feature,
  CONSTRAINT dominantedgegrid8_pkey PRIMARY KEY (fet_pic_id),
  CONSTRAINT dominantedgegrid8_fet_pic_id_fkey FOREIGN KEY (fet_pic_id)
      REFERENCES picture(pic_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE INDEX dominantedgegrid8_fet_pic_id_idx
  ON dominantedgegrid8
  USING btree
  (fet_pic_id);


DROP FUNCTION IF EXISTS reset_indices();
CREATE FUNCTION reset_indices() RETURNS VOID AS $$
DECLARE

BEGIN
	CREATE VA ON dominantedgegrid16 (edges) USING EQUIFREQUENT MARKS;
	CREATE VA ON dominantedgegrid8 (edges) USING EQUIFREQUENT MARKS;
	CREATE VA ON edgearp88 (arp) USING EQUIFREQUENT MARKS;
	CREATE VA ON edgearp88full (arp) USING EQUIFREQUENT MARKS;
	CREATE VA ON edgegrid16 (grid) USING EQUIFREQUENT MARKS;
	CREATE VA ON edgegrid16full (grid) USING EQUIFREQUENT MARKS;
	CREATE VA ON ehd (hist) USING EQUIFREQUENT MARKS;
	
END;
 $$ LANGUAGE plpgsql;
 
 



--generate initialise data:
INSERT INTO ResearchGroup(reg_name) VALUES ('testgroup');
INSERT INTO UserRole(uro_name, uro_addUser,uro_editUser,uro_deleteUser,uro_createResearchGroup,uro_editResearchGroup,
uro_deleteResearchGroup,uro_editOwnResearchGroup,uro_deleteOwnResearchGroup,uro_uploadNewWatermark,uro_uploadNewIphType,
uro_editAllWatermarks,uro_editWaterMarksOfResearchGroup,uro_editAllIphTypes,uro_editIphTypesOfResearchGroup)
VALUES(
'searcher', false, false, false, false, false, false, false, false, false, false, false, false, false, false
),
(
'editor', false, false, false, false, false, false, false, false, true, true, false, true, false, true
),
(
'groupadmin', true, true, true, false, false, false, true, false, true, true, false, true, false, true
),
(
'superuser', true, true, true, true, true, true, true, true, true, true, true, true, true, true
);
INSERT INTO users (usr_email, usr_pw_hash, usr_pw_salt, usr_validation_status, usr_validation_hash) VALUES 
('lucius.bachmann@gmx.ch', 'f4021e137b8dc9af5924f288ab885a466f60657ff4c7ea91c4f7c2a42d1f4c007d08a1bd1198cbc9d952df70457a4c85238c569cb71a37bba29f30664dd07ced9329d510745fc65','9329d510745fc65',  true, '');
INSERT INTO usr_uro_reg(uur_usr_id, uur_uro_id, uur_reg_id) VALUES(1,4,1);
--password is password
