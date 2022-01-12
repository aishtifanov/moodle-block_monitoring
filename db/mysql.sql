# Таблицы Мониторинга образовательных учреждений

CREATE TABLE  `prefix_mdl_monit_options` (
  `id` int(10) NOT NULL auto_increment,
  `idtypeschool` int(10) unsigned NOT NULL,
  `idfield` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE  `prefix_monit_school_type` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `prefix_monit_school` (`name`) VALUES ('Начальная общеобразовательная школа (I ступень)');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Образовательное учреждение для детей дошкольного и младшего школьного возраста');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Основная общеобразовательная школа (I-II ступени)');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Средняя (полная) общеобразовательная школа (I-III ступени)');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Средняя (полная) общеобразовательная школа с углубленным изучением отдельных предметов');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Лицей');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Гимназия');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Вечерняя (сменная) общеобразовательная школа');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Открытая (сменная) общеобразовательная школа');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Центр образования');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Кадетская школа');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Образовательное учреждение для детей с отклонениями в развитии');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Учебно-воспитательное учреждение для детей с девиантным поведением');
INSERT INTO `prefix_monit_school` (`name`) VALUES ('Санаторно-лесная школа');

CREATE TABLE `prefix_monit_form_rkp_f` (
  `id` int(10) NOT NULL auto_increment,
  `levelmonit` varchar(15) NOT NULL default 'region',
  `rayonid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `schoolid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `finstatus` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `contract_type` tinyint(3) default '0',
  `conсurs_name` varchar(255) default '',
  `concurs_open` date,
  `concurs_close` date,
  `conсurs_link` varchar(255) default '',
  `contract_name` varchar(255) default '',
  `contract_summa` double default 0,
  `contract_open` date,
  `contract_close` date,
  `contract_fin_doc` varchar(255) default '',
  PRIMARY KEY  (`id`),
	INDEX rayonid(`rayonid`),
  INDEX schoolid(`schoolid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `prefix_monit_form_rkp_f_dir` (
  `id` int(10) NOT NULL auto_increment,
  `rkp_f_id` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `directionid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `summa` double default 0,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `prefix_monit_form_rkp_f_pay` (
  `id` int(10) NOT NULL auto_increment,
  `rkp_f_id` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `number` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `paydate` date,
  `summa` double default 0,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Таблица - справочник направления расходования средств
# id - код направления
# name - название направления
CREATE TABLE `prefix_monit_direction` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `prefix_monit_direction` (`name`) VALUES ('1. Развитие региональной системы оценки качества образования (СОКО), системы аттестации ОУ');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('1.1. Оборудование для видеоконференц-зала регионального Центра оценки качества образования');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('1.2. Компьютерное оборудование для регионального Центра оценки качества образования');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('1.3. Ремонт помещений для  регионального центра оценки качества образования');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('2. Развитие сети ОУ:  обеспечение условий для получения качественного общего образования');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('2.1. Текущий ремонт');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('2.2. Приобретение комплектов  ученической мебели для базовых (опорных) школ');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('2.3. Разработка опытного  образца Интрасети для ОУ области');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('2.4. Серверное оборудование  для второй очереди Интрасети ОУ области');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('2.5. Приобретение  оборудования для базовых (опорных) школ');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('3. Организационное обеспечение реализации комплексного проекта модернизации образования области');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('3.1. Оказание услуг по подготовке кадров по КПМО Белгородской области в 2007г.');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('3.2. Подготовка педагогических работников, осуществляющих профильное обучение на старшей ступени');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('3.3. Обучающие семинары');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('3.4. Региональная научно-практическая конференция');
INSERT INTO `prefix_monit_direction` (`name`) VALUES ('3.5. Курсы переподготовки педагогических кадров');

# Таблица - данные формы БКП-ф(ООУ)
# id - код формы
# listformid - код формы в списке школьных форм
CREATE TABLE `prefix_monit_form_bkp_f` (
	`id` int(10) NOT NULL auto_increment,
	`listformid` int(10) NOT NULL default '0',
	`f1f` double  default NULL,
	`f2f` double  default NULL,
	`f3f` double  default NULL,
	`f4f` double  default NULL,
	`f5f` double  default NULL,
	`f6f` double  default NULL,
	`f7f` double  default NULL,
	`f8f` double  default NULL,
	`f9f` double  default NULL,
	`f10f` double  default NULL,
  `f11f` double  default NULL,
  `f12f` double  default NULL,
  `f13f` double  default NULL,
  `f14f` double  default NULL,
  `f15f` double  default NULL,
	PRIMARY KEY (`id`),
	INDEX listformid(`listformid`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;


# Таблица - данные формы БКП-дл(ООУ)
# id - код формы
# listformid - код формы в списке школьных форм
CREATE TABLE `prefix_monit_form_bkp_dolj` (
	`id` int(10) NOT NULL auto_increment,
	`listformid` int(10) NOT NULL default '0',
	`r_dir` integer default NULL,
	`r_zam_dir` integer default NULL,
	`r_upr_uch_choz` integer default NULL,
	`r_ruk_str_podrazd` integer default NULL,
	`r_gl_buch` integer default NULL,
	`r_gl_ing` integer default NULL,
	`r_zav_fil_bibl` integer default NULL,
	`r_zav_arch` integer default NULL,
	`r_zav_mnogit_buro` integer default NULL,
	`r_zav_lab` integer default NULL,
	`r_zav_choz` integer default NULL,
	`r_zav_machin_buro` integer default NULL,
	`r_zav_koncel` integer default NULL,
	`r_zav_sklad` integer default NULL,
	`r_nach_choz_otd` integer default NULL,
	`r_nach_uchastka` integer default NULL,
	`r_nach_otd` integer default NULL,
	`r_nach_cecha` integer default NULL,
	`r_dir_kot` integer default NULL,
	`r_nach_gor` integer default NULL,
	`r_nach_master` integer default NULL,
	`r_zav_obchegit` integer default NULL,
	`r_zav_stol` integer default NULL,
	`r_upr_o` integer default NULL,
	`r_nach_vspom_o` integer default NULL,
	`r_zav_pr` integer default NULL,
	`r_prep_stager` integer default NULL,
	`r_ass_prep` integer default NULL,
	`r_st_prep` integer default NULL,
	`r_docent` integer default NULL,
	`r_prof` integer default NULL,
	`r_zav_kafedr` integer default NULL,
	`r_dekan` integer default NULL,
	`r_rektor` integer default NULL,
	`r_dir_fil` integer default NULL,
	`r_nach_plan_fin` integer default NULL,
	`r_nach_uch_upr` integer default NULL,
	`r_nach_otd_tso` integer default NULL,
	`r_nach_otd_ot_tb` integer default NULL,
	`r_gl_ing_jur` integer default NULL,
	`r_gl_energet` integer default NULL,
	`r_uch_sekret` integer default NULL,
	`r_ruk_stud_issl_buro` integer default NULL,
	`s_uch` integer default NULL,
	`s_prep` integer default NULL,
	`s_uch_defekt` integer default NULL,
	`s_prep_org` integer default NULL,
	`s_ruk_fis_vosp` integer default NULL,
	`s_master_pr_obuch` integer default NULL,
	`s_metodist` integer default NULL,
	`s_koncert` integer default NULL,
	`s_mus_ruk` integer default NULL,
	`s_akk` integer default NULL,
	`s_vosp` integer default NULL,
	`s_kl_vosp` integer default NULL,
	`s_soc_ped` integer default NULL,
	`s_ped_psich` integer default NULL,
	`s_ped_org` integer default NULL,
	`s_ped_dop_obr` integer default NULL,
	`s_trener` integer default NULL,
	`s_st_vog` integer default NULL,
	`s_instr_trud` integer default NULL,
	`s_instr_fis_kult` integer default NULL,
	`s_dispetch` integer default NULL,
	`s_inspect` integer default NULL,
	`s_labor` integer default NULL,
	`s_technik` integer default NULL,
	`s_buch` integer default NULL,
	`s_buch_rev` integer default NULL,
	`s_ingener` integer default NULL,
	`s_ekonomist` integer default NULL,
	`s_tov_ved` integer default NULL,
	`s_chud` integer default NULL,
	`s_jur` integer default NULL,
	`s_elektronik` integer default NULL,
	`s_programm` integer default NULL,
	`s_doc_ved` integer default NULL,
	`s_surdo` integer default NULL,
	`s_spec_kadr` integer default NULL,
	`s_admin` integer default NULL,
	`uvp_bibl` integer default NULL,
	`uvp_deg_obch` integer default NULL,
	`uvp_deg_regim` integer default NULL,
	`uvp_ml_vosp` integer default NULL,
	`uvp_mech` integer default NULL,
	`uvp_pom_vosp` integer default NULL,
	`uvp_sekr_uch` integer default NULL,
	`tiop_ekspeditor` integer default NULL,
	`tiop_delopr` integer default NULL,
	`tiop_sekr` integer default NULL,
	`tiop_sekr_mash` integer default NULL,
	`tiop_archiv` integer default NULL,
	`tiop_kassir` integer default NULL,
	`tiop_mash` integer default NULL,
	`tiop_ekspeditor_grus` integer default NULL,
	`tiop_sekr_nezr_spec` integer default NULL,
	`tiop_komend` integer default NULL,
	`tiop_degurn` integer default NULL,
	`tiop_pasp` integer default NULL,
	`tiop_chim1` integer default NULL,
	`tiop_chim2` integer default NULL,
	`tiop_chim3` integer default NULL,
	`tiop_chim4` integer default NULL,
	`tiop_voz` integer default NULL,
	`tiop_vod4` integer default NULL,
	`tiop_vod5` integer default NULL,
	`tiop_vod6` integer default NULL,
	`tiop_vod_trans_ub` integer default NULL,
	`tiop_garder` integer default NULL,
	`tiop_gornich` integer default NULL,
	`tiop_grus1` integer default NULL,
	`tiop_grus2` integer default NULL,
	`tiop_dvor` integer default NULL,
	`tiop_desinf2` integer default NULL,
	`tiop_desinf3` integer default NULL,
	`tiop_istop` integer default NULL,
	`tiop_kast1` integer default NULL,
	`tiop_kast2` integer default NULL,
	`tiop_kinomech2` integer default NULL,
	`tiop_kinomech3` integer default NULL,
	`tiop_kinomech4` integer default NULL,
	`tiop_kinomech5` integer default NULL,
	`tiop_klad1` integer default NULL,
	`tiop_klad2` integer default NULL,
	`tiop_kon1` integer default NULL,
	`tiop_kon2` integer default NULL,
	`tiop_lift` integer default NULL,
	`tiop_labor2` integer default NULL,
	`tiop_labor3` integer default NULL,
	`tiop_labor4` integer default NULL,
	`tiop_labor5` integer default NULL,
	`tiop_mech_tech_sport` integer default NULL,
	`tiop_nn` integer default NULL,
	`tiop_op_kot` integer default NULL,
	`tiop_op_kot3` integer default NULL,
	`tiop_op_kot4` integer default NULL,
	`tiop_op_kot5` integer default NULL,
	`tiop_op_kot6` integer default NULL,
	`tiop_op_mikr3` integer default NULL,
	`tiop_op_mikr4` integer default NULL,
	`tiop_op_kopir` integer default NULL,
	`tiop_op_evvm` integer default NULL,
	`tiop_op_evvm3` integer default NULL,
	`tiop_op_evvm4` integer default NULL,
	`tiop_parik` integer default NULL,
	`tiop_pr1` integer default NULL,
	`tiop_pr2` integer default NULL,
	`tiop_pl3` integer default NULL,
	`tiop_pl4` integer default NULL,
	`tiop_pl5` integer default NULL,
	`tiop_pl6` integer default NULL,
	`tiop_korz` integer default NULL,
	`tiop_korz3` integer default NULL,
	`tiop_korz4` integer default NULL,
	`tiop_st3` integer default NULL,
	`tiop_st4` integer default NULL,
	`tiop_st5` integer default NULL,
	`tiop_st6` integer default NULL,
	`tiop_sl2` integer default NULL,
	`tiop_sl3` integer default NULL,
	`tiop_sl4` integer default NULL,
	`tiop_sl5` integer default NULL,
	`tiop_sl6` integer default NULL,
	`tiop_rst_i_rem` integer default NULL,
	`tiop_rgiv` integer default NULL,
	`tiop_rsport` integer default NULL,
	`tiop_sad1` integer default NULL,
	`tiop_sad2` integer default NULL,
	`tiop_st_vacht` integer default NULL,
	`tiop_trakt` integer default NULL,
	`tiop_ub_pom` integer default NULL,
	`tiop_ub_terr` integer default NULL,
	`tiop_filmprov3` integer default NULL,
	`tiop_filmprov4` integer default NULL,
	PRIMARY KEY (`id`),
	INDEX listformid(`listformid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - данные формы БКП-пред(ООУ)
# id - код формы
# listformid - код формы в списке школьных форм
CREATE TABLE `prefix_monit_form_bkp_pred` (
	`id` int(10) NOT NULL auto_increment,
	`listformid` int(10) NOT NULL default '0',
	`ku_biologia` integer default NULL,
	`ku_geografia` integer default NULL,
	`ku_estestvozn` integer default NULL,
	`ku_in_jaz` integer default NULL,
	`ku_ikt` integer default NULL,
	`ku_iskusstvo` integer default NULL,
	`ku_istoria` integer default NULL,
	`ku_literatura` integer default NULL,
	`ku_lit_chtenie` integer default NULL,
	`ku_matem` integer default NULL,
	`ku_mhk` integer default NULL,
	`ku_obved` integer default NULL,
	`ku_obzn` integer default NULL,
	`ku_obzn_pek` integer default NULL,
	`ku_okr_mir` integer default NULL,
	`ku_osn_agr` integer default NULL,
	`ku_obg` integer default NULL,
	`ku_osn_giv` integer default NULL,
	`ku_pravo` integer default NULL,
	`ku_pkult` integer default NULL,
	`ku_prirodoved` integer default NULL,
	`ku_prof_isk` integer default NULL,
	`ku_rus_jaz` integer default NULL,
	`ku_schoz_techn` integer default NULL,
	`ku_techn` integer default NULL,
	`ku_techn_et` integer default NULL,
	`ku_techn_ikt` integer default NULL,
	`ku_phisika` integer default NULL,
	`ku_phis_kult` integer default NULL,
	`ku_chim` integer default NULL,
	`ku_ek_bo` integer default NULL,
	`ku_ekonom` integer default NULL,
	PRIMARY KEY (`id`),
	INDEX listformid(`listformid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Таблица - данные формы РКП-у(ООУ)
# id - код формы
# listformid - код формы в списке школьных форм
CREATE TABLE `prefix_monit_form_rkp_u` (
	`id` int(10) NOT NULL auto_increment,
	`listformid` int(10) NOT NULL default '0',
  `f0_1u` int(10) default NULL,
  `f0_2u` int(10) default NULL,
  `f0_3u` int(10) default NULL,
  `f0_4u` int(10) default NULL,
  `f0_5u` double  default NULL,
  `f0_6u` double  default NULL,
  `f0_7u` double  default NULL,
  `f0_8u` tinyint(3) default '0',
  `f0_9u` int(10) default NULL,
  `f0_10u` int(10) default NULL,
  `f0_11u` int(10) default NULL,
  `f1_5u` tinyint(3) default '0',
  `f1_5g1` int(10) default NULL,
  `f1_5g2` varchar(255) default '',
  `f1_5g3` varchar(255) default '',
  `f2_1u` tinyint(3) default '0',
  `f2_3u` tinyint(3) default '0',
  `f4_0_1` tinyint(3) default '0',
  `f4_0_2` tinyint(3) default '0',
  `f4_0_3` tinyint(3) default '0',
  `f4_1_8` tinyint(3) default '0',
  `f4_1_9` tinyint(3) default '0',
  `f5_1u` tinyint(3) default '0',
  `f5_1g1` int(10) default NULL,
  `f5_1g2` varchar(255) default '',
  `f5_1g3` varchar(255) default '',
  `f5_3_0u` tinyint(3) default '0',
  `f5_3g` varchar(255) default '',
  `f5_4_1u` tinyint(3) default '0',
  `f5_4g` varchar(255) default '',
	PRIMARY KEY (`id`),
	INDEX listformid(`listformid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Таблица - данные формы РКП-д/у(РО)
# id - код формы
# listformid - код формы в списке школьных форм
CREATE TABLE `prefix_monit_form_rkp_du` (
	`id` int(10) NOT NULL auto_increment,
	`listformid` int(10) NOT NULL default '0',
	`fd_u_1` text  default '',
	`fd_u_2` text  default '',
	PRIMARY KEY  (`id`),
	INDEX listformid(`listformid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - СПИСОК ШКОЛЬНЫХ ФОРМ
# id - код формы
# schoolid - код региона
CREATE TABLE  `prefix_monit_school_listforms` (
  `id` int(10) NOT NULL auto_increment,
  `rayonid` int(10) NOT NULL default '0',
  `schoolid` int(10) NOT NULL default '0',
  `status` tinyint(3) unsigned NOT NULL default '1',
  `shortname` varchar(15) NOT NULL default '',
  `shortrusname` varchar(20) NOT NULL default '',
  `fullname` varchar(255) default NULL,
  `datemodified` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `schoolid` (`schoolid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - данные формы РКП-пр/м(МО)
# id - код формы
# listformid - код формы в списке районных форм
CREATE TABLE `prefix_monit_form_rkp_prm_eks` (
	`id` int(10) NOT NULL auto_increment,
	`listformid` int(10) NOT NULL default '0',
  `f5_2m` tinyint(3) default '0',
  `f6_3_7_1` tinyint(3) default '0',
  `f6_3_7_2` tinyint(3) default '0',
  `f6_3_7_3` tinyint(3) default '0',
  `f6_3_7_4` tinyint(3) default '0',
  `f6_3_7_5` tinyint(3) default '0',
	PRIMARY KEY  (`id`),
	INDEX listformid(`listformid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - данные формы РКП-пр/м(МО)
# id - код формы
# listformid - код формы в списке районных форм
CREATE TABLE `prefix_monit_form_rkp_prm_mo` (
	`id` int(10) NOT NULL auto_increment,
	`listformid` int(10) NOT NULL default '0',
  `f0_0_0m` tinyint(3) default '0',
  `f5_2_0m` varchar(200)  default NULL,
	PRIMARY KEY  (`id`),
	INDEX listformid(`listformid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - СПИСОК РАЙОННЫХ ФОРМ
# id - код формы
# rayonid - код района
CREATE TABLE  `prefix_monit_rayon_listforms` (
  `id` int(10) NOT NULL auto_increment,
  `rayonid` int(10) NOT NULL default '0',
  `status` tinyint(3) unsigned NOT NULL default '1',
  `shortname` varchar(15) NOT NULL default '',
  `shortrusname` varchar(20) NOT NULL default '',
  `fullname` varchar(255) default NULL,
  `datemodified` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `rayonid` (`rayonid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - данные формы РКП-пр/р(РЭ)
# id - код формы
# listformid - код формы в списке региональных форм
CREATE TABLE `prefix_monit_form_rkp_prr_eks` (
	`id` int(10) NOT NULL auto_increment,
	`listformid` int(10) NOT NULL default '0',
  `f6_1r`  tinyint(3) default '0',
  `f6_1_1` tinyint(3) default '0',
  `f6_2_1` tinyint(3) default '0',
  `f6_2_2` tinyint(3) default '0',
  `f6_2_3` tinyint(3) default '0',
  `f6_2_4` tinyint(3) default '0',
  `f6_2_5` tinyint(3) default '0',
  `f6_3_1` tinyint(3) default '0',
  `f6_3_2` tinyint(3) default '0',
  `f6_3_3` tinyint(3) default '0',
  `f6_3_4` tinyint(3) default '0',
  `f6_3_5` tinyint(3) default '0',
	PRIMARY KEY  (`id`),
	INDEX listformid(`listformid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - данные формы РКП-пр/р(РО)
# id - код формы
# rkp_prr_ro_id - код формы
CREATE TABLE `prefix_monit_form_rkp_prr_ro` (
	`id` int(10) NOT NULL auto_increment,
	`listformid` int(10) NOT NULL default '0',
  `f1_2_0` double  default NULL,
  `f2_4_1r` double  default NULL,
  `f2_4_1g` varchar(200)  default NULL,
  `f2_4_1_0` double  default NULL,
  `f2_4_1_0g` varchar(200)  default NULL,
  `f2_4_2r` double  default NULL,
  `f2_4_2g` varchar(200)  default NULL,
  `f2_4_2_0` double default NULL,
  `f2_4_2_0g` varchar(200)  default NULL,
  `f2_5_1r` int(10)  default NULL,
  `f2_5_1g` varchar(200)  default NULL,
  `f2_6_1g` varchar(200)  default NULL,
  `f2_6_2g` varchar(200)  default NULL,
  `f3_6_1g` varchar(200)  default NULL,
  `f3_6_2g` varchar(200)  default NULL,
  `f3_7_1g` varchar(200)  default NULL,
  `f3_7_2g` varchar(200)  default NULL,
  `f3_8g` varchar(200) default NULL,
  `f4_5_0` int(10)  default NULL,
	PRIMARY KEY  (`id`),
	INDEX listformid(`listformid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - данные формы РКП-егэ
# id - код формы
# rkp_ege_id - код формы
CREATE TABLE `prefix_monit_form_rkp_ege` (
	`id` int(10) NOT NULL auto_increment,
	`listformid` int(10) NOT NULL default '0',
  `f3_2` int(10)  default NULL,
  `f3_3_1r` int(10)  default NULL,
  `f3_3_2r` int(10)  default NULL,
  `f3_4r` int(10)  default NULL,
  `f3_5_1r` int(10)  default NULL,
  `f3_5_2r` int(10)  default NULL,
  `f5_5_1` int(10)  default NULL,
  `f5_5_2` int(10)  default NULL,
	PRIMARY KEY  (`id`),
	INDEX listformid(`listformid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - данные формы РКП-д
# id - код формы
# rkp_d_id - код формы
CREATE TABLE `prefix_monit_form_rkp_d` (
	`id` int(10) NOT NULL auto_increment,
	`listformid` int(10) NOT NULL default '0',
  `fd1_` text default '',
  `fd2_` text default '',
  `fd3_` text default '',
  `fd4_` text default '',
  `fd5_` text default '',
  `fd6_` text default '',
  `fd7_` text default '',
  `fd8_` text default '',
  `fd8_0` text default '',
  `fd8_1` text default '',
  `fd8_2` text default '',
  `fd9_` text default '',
  `fd9_0` text default '',
  `fd9_1` text default '',
  `fd9_2` text default '',
  `fd10_` text default '',
  `fd11_` text default '',
  `fd12_` text default '',
  `fd13_` text default '',
  `fd14_` text default '',
  `fd15_` text default '',
  `fd16_` text default '',
  `fd17_` text default '',
	PRIMARY KEY  (`id`),
	INDEX listformid(`listformid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - СПИСОК РЕГИОНАЛЬНЫХ ФОРМ
# id - код формы
# regionid - код региона
CREATE TABLE  `prefix_monit_region_listforms` (
  `id` int(10) NOT NULL auto_increment,
  `regionid` int(10) NOT NULL default '0',
  `status` tinyint(3) unsigned NOT NULL default '1',
  `shortname` varchar(15) NOT NULL default '',
  `shortrusname` varchar(20) NOT NULL default '',
  `fullname` varchar(255) default NULL,
  `datemodified` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `regionid` (`regionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - район
# id - код района
# regionid - код региона
# headid - код главы района
# name - название района
CREATE TABLE `prefix_monit_rayon` (
	`id` int(10) NOT NULL auto_increment,
	`regionid` int(10) NOT NULL default '0',
	`number` int(10) NOT NULL default '0',
	`name` varchar(255) NOT NULL default '',
  `headid` int(10)  default '0',
	`fio` varchar(100) default NULL,
	`phones` varchar(100) default NULL,
	`address` varchar(255) default NULL,
	`filemap` varchar(20) default NULL,
	`timemodified` int(10) default NULL,
	PRIMARY KEY  (`id`),
	INDEX regionid(`regionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,1, 'Алексеевский район и город Алексеевка', 'alekseevka.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,2, 'Белгородский район','belregion.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,3, 'Борисовский район','borisovka.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,4, 'Валуйский район и город Валуйки','valujskij.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,5, 'Вейделевский район','veidelevka.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,6, 'Волоконовский район','volokonovka.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,7, 'Грайворонский район','graivoron.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,8, 'Губкинский район и город Губкин','gubkin.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,9, 'Ивнянский район','ivnya.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,10, 'Корочанский район','korocha.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,11, 'Красненский район', 'krasnen.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,12, 'Красногвардейский район','biryuch.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,13, 'Краснояружский район','krasnoyar.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,14, 'Новооскольский район','novosk.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,15, 'Прохоровский район','prohorovka.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,16, 'Ракитянский район', 'rakitnoe.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,17, 'Ровеньской район','rovenki.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,18, 'Чернянский район','chernyanka.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,19, 'Шебекинский район и город Шебекино', 'shebekino.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,20, 'Яковлевский район','yakovlev.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,21, 'Город Белгород','belg.crd');
INSERT INTO `prefix_monit_rayon` (`regionid`, `number`, `name`, `filemap`)  VALUES (1,22, 'Город Старый Оскол и Старооскольский район','stoskol.crd');

# Таблица - регион
# id - код региона
# headid - код главы региона
# name - название региона
CREATE TABLE `prefix_monit_region` (
	`id` int(10) NOT NULL auto_increment,
	`name` varchar(100) NOT NULL default '',
	`headid` int(10)  default '0',
	`timemodified` int(10) default NULL,
	PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `prefix_monit_region` (`name`)  VALUES ('Белгородская область');

# Таблица - региональные администраторы мониторинга
# id - код администратора
# regionid - код области
# userid - код пользователя
CREATE TABLE `prefix_monit_operator_region` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `regionid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `userid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`id`),
  INDEX `regionid`(`regionid`),
  INDEX `userid`(`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - муниципальные операторы мониторинга
# id - код оператора
# rayonid - код района
# userid - код пользователя
CREATE TABLE `prefix_monit_operator_rayon` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `rayonid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `userid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`id`),
  INDEX `rayonid`(`rayonid`),
  INDEX `userid`(`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Таблица - школьные операторы мониторинга
# id - код оператора
# schoolid - код школы
# userid - код пользователя
CREATE TABLE `prefix_monit_operator_school` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `schoolid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `userid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`id`),
  INDEX `schoolid`(`schoolid`),
  INDEX `userid`(`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Таблица - школа
# id - код школы
# rayonid - код района
# headid - код директора школы
# name - название школы
CREATE TABLE `prefix_monit_school` (
	`id` int(10) NOT NULL auto_increment,
	`rayonid` int(10) NOT NULL default '0',
	`number` int(10) NOT NULL default '0',
	`name` varchar(255) NOT NULL default '',
  `headid` int(10)  default '0',
  `fio` varchar(100) default NULL,
  `phones` varchar(100) default NULL,
 	`address` varchar(255) default NULL,
  `www` varchar(255) default NULL,
  `email` varchar(255) default NULL,
	`timemodified` int(10) default NULL,
	PRIMARY KEY  (`id`),
	INDEX rayonid(`rayonid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,1,'МОУ СОШ №1 г. Алексеевка','Харченко Евгения Петровна','(8-234)3-41-66','309850, г.Алексеевка, Ул. Ф. Энгельса д.6','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,2,'МОУ СОШ №2 г. Алексеевка','Колядин Василий Васильевич','(8-234)3-44-22','309850, г. Алексеевка, ул. Л. Толстого д. 10','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,3,'МОУ СОШ №3 г. Алексеевка с углублённым изучением отдельных предметов','Лымарь Людмила Александровна','(8-234)3-45-46','309850, г. Алексеевка, ул. В. Собины д.10','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,4,'МОУ СОШ №4 г. Алексеевка','Ковалёв Михаил Иванович','(8-234)3-50-88','309852, г. Алексеевка,ул. Комсомольская, д. 51','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,5,'МОУ СОШ №5 г. Алексеевка','Передрий Виктор Николаевич','(8-234)3-52-55','309850, г. Алексеевка,ул. Гагарина, д.14','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,6,'МОУ СОШ №6 г. Алексеевка','Савченко Галина Григорьевна','(8-234)3-55-44','309853, г. Алексеевка,ул. Чкалова,  д.63','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,7,'МОУ СОШ №7 г. Алексеевка','Косненников Василий Иванович','','309850, г. Алексеевка, ул. Урицкого, д. 91','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,8,'МОУ Алейниковская СОШ','Козыренко Сергей Владимирович','(8-234)7-54-42','309812, с.Алейниково','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,9,'МОУ Афанасьевская СОШ','Сапелкин Николай Тимофеевич','(8-234)5-67-31','309834, с.Афанасьевка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,10,'МОУ Божковская СОШ','Шкуропат Светлана Ивановна','(8-234)5-56-41','309825, с Божково','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,11,'МОУ Варваровская  СОШ','Шацкий Иван Григорьевич','(8-234)7-42-17','309813, с.Варваровка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,12,'МОУ Глуховская СОШ','Ожерельев Алексей Иванович','(8-234)7-31-29','309831, с. Глуховка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,13,'МОУ Жуковская СОШ','Падалко Людмила Петровна','(8-234)7-35-17','309806, с.Жуково','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,14,'МОУ Иващенковская СОШ','Головина Людмила Николаевна','(8-234)7-53-32','309822, с.Иващенково','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,15,'МОУ Иловская СОШ им. Героя России В.Бурцева','Ковалёв Владимир Андреевич','(8-234)7-24-75','309830, с.Иловка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,16,'МОУ  Ильинская СОШ','Ярковой Юрий Акимович','(8-234)5-64-91','309802, с.Ильинка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,17,'МОУ Красненская СОШ','Дегтярёв Алексей Григорьевич','(8-234)5-43-73','309814, с.Красное','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,18,'МОУ Луценковская СОШ','Панченко Анатолий Анатольевич','(8-234)7-47-21','309824, с. Луценково','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,19,'МОУ Мухоудеровская СОШ','Харченко Алексей Николаевич','(8-234)7-36-40','309825, с  Мухоудеровка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,20,'МОУ Матрёногезовска СОШ','Кравченко Надежда Александровна','(8-234)7-55-95','309820, с.Матрёногезово','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,21,'МОУ Меняйловская СОШ','Курепко Алла Николаевна','(8-234)5-51-23','309811, с.Меняйлово','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,22,'МОУ  Подсередненская СОШ','Ярцева Надежда Николаевна','(8-234)5-55-44','309821, с.Подсереднее','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,23,'МОУ Репенская СОШ','Курдяева Татьяна Алексеевна','(8-234)5-45-36','309832, с.Репенка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,24,'МОУ Советская СОШ','Бережная Раиса Ивановна','(8-234)7-51-67','309816, с.Советское','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,25,'МОУ Хлевищенская СОШ','Попова Зинаида Ивановна','(8-234)5-45-61','309840, с. Хлевище','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,26,'МОУ Хрещатовская СОШ','Зенина Людмила Евгеньевна','(8-234)5-12-36','309805, с. Хрещатое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,27,'МОУ Щербаковская СОШ','Божко Тамара Тихоновна','(8-234)7-65-22','309803, с.Щербаково','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (1,28,'МОУ Тютюниковская СОШ','Почкун Татьяна Петровна',   '(8-234)5-53-16', '309823, с.Тютюниково','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,1,'МОУ Гимназия № 1','Гребенников Юрий Борисович','27-43-14, Ф:27-95-86','308001, г.Белгород,ул. Чумичева, д.53-А','','belgschool1@ yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,2,'МОУ Гимназия № 2','Работягова Эльвира Геннадьевна','34-30-60,34-18-15','308023, г. Белгород,ул.Некрасова, д.19','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,3,'МОУ Гимназия № 3','Заморозова Галина Ивановна','27-53-31,27-02-27','308000, г.Белгород,Проспект Славы, д.13','',' sсhool03bel@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,4,'МОУ СОШ № 4','Самойлова Татьяна Николаевна','32-90-95, Ф:32-52-18','308012,г.Белгород,ул.Победы, д.78','','kid4@belnet.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,5,'МОУ Гимназия № 5','Нижегородцев Юрий Сергеевич','27-35-22,27-72-40,27-35-71, Ф:27-35-22','308800, г.Белгород,ул.Победы, д. 40-А','','sсhool05@inbox.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,6,'МОУ СОШ № 7','Корж Антонина Сергеевна','34-15-41,34-07-30','308023, г. Белгород,ул. Железнякова, д.4','','sсhool7@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,7,'МОУ СОШ №8','Немыкина Алла Григорьевна','32-14-23','308012, г. Белгород,Народный бульвар,д.118','','Sсhool8bel@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,8,'МОУ Лицей № 9','Винокурова Лидия Александровна','32-35-36,35-61-84','308000, г.Белгород,Народный бульвар, д.74','','school09bel@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,9,'МОУ Лицей № 10','Кириллова Евгения Ивановна','25-01-36,25-26-80','308024, г. Белгород,ул.Мокроусова, д.3-А','www.mou-licey10.narod.ru','Аlicey10bel@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,10,'МОУ СОШ № 11','Легурда Елена Кимовна','21-15-55,21-15-41,21-11-23','308013,г. Белгород, пер.Макаренко, д.3','','sсhool11@list.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,11,'МОУ Гимназия №12','Норцова Раиса Алендровна','26-45-17,26-46-41','308014,г.Белгород,ул. капитана Хихлушки, д.4','','sсhooll12bel@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,12,'МОУ СОШ № 13','Дегтярёва Эмма Юрьевна','25-33-51,25-62-74','308019, г.Белгород,ул.Горького 26-А','','degtyareva13@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,13,'МОУ СОШ №14','Косенко Ирина Алексадровна','34-12-66','308010,г.Белгород,ул. Крупской, д.9','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,14,'МОУ СОШ № 15','Козловцева Анна Викторовна','27-66-09','308018, г.Белгород,ул. Волчанская, д.22','','abraziv@bel.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,15,'МОУ СОШ №16','Шманенко Тамара Юрьевна','30-27-59','308001, г. Белгород,ул. Октябрьская, д. 26-А','','shool16bel@.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,16,'МОУ СОШ № 17 ','Сиденко Ирина Эдуардовна','34-82-06','308010, г.Белгород,ул. 1-Центральная, д.20','','shool17bel@.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,17,'МОУ СОШ № 18','Андреева Виктория Николаевна','21-17-23, 21-36-90','308017, г. Белгород,ул.Репина, д. 3-А','','sсhool18@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,18,'МОУ СОШ №19','Нестеренко Светлана Ивановна','32-35-16','308000, г.Белгородул.Преображенская, 98','','belshool19@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,19,'МОУ СОШ № 20','Маслова Валентина Алексеевна','26-17-37,26-07-66','308007, г. Белгород,ул.Шершнёва, д. 26','http://www.belgorod.fio.ru/sites/school20/index.htm','zemsch20@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,20,'МОУ СОШ № 21','Никитинский Андрей Васильевич','32-03-48, 32-11-60, Ф:36-98-57','308015, г. Белгород, ул.Чапаева, д.14','','sсhool21@.bel.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,21,'МОУ Гимназия № 22','Шляхова Светлана Анатольевна','51-03-77,51-03-73, Ф:51-06-12','308036,г.Белгород,Бульвар Юности, д.14','','Gimnas22@bel.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,22,'МОУ СОШ № 24','Михайлова Ольга Алексеевна','21-79-01','308018, г.Белгород,ул.Корочанская,318','','dtal940@ yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,23,'МОУ СОШ №27','Головчанская Любовь Михайловна','34-17-84, 34-10-45','308023, г. Белгород,ул. Некрасова, д. 20','','sсhool27@belgorod edu.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,24,'МОУ СОШ №28','Денисов Николай Тихонович','25-05-63, 25-40-00','308012, г. Белгород,ул. Щорса, д. 11','','sсhool28@belgorod edu.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,25,'МОУ СОШ №29','Фатеева Екатерина Александровна','34-08-08, 34-18-85','308023, г. Белгород,ул. Некрасова, д. 38-А','','sсhool29bel@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,26,'МОУ СОШ №31','И.о. Гричихина Ирина Анатольевна','25-06-87','308024, г. Белгород,ул. Костюкова, д. 20','','sсhool31belgorod@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,27,'МОУ Лицей №32','Перестенко Николай Васильевич','26-47-08, 26-87-89, 26-47-59','308002, г. Белгород,ул. Мичурина, д. 39','','sсhool32belgс@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,28,'МОУ СОШ №33','Мамин Олег Викторович','31-69-83','308012, г. Белгород,проспект Славы, д. 546','','sсhool33bel@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,29,'МОУ СОШ №34','Волошенко Николай Павлович','20-07-77, 25-34-79','308012, г. Белгород,ул. 8 Марта, д. 172','','sсhool34bel@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,30,'МОУ СОШ №35','Ракитянская Татьяна Николаевна','30-20-84','308012, г. Белгород,ул. Преображенская, д. 14','','Sсhool35@yandex.ru ');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,31,'МОУ СОШ №36','Степанченко Виолетта Григорьевна','25-44-10, 25-32-01, 25-12-98','308012, г. Белгород,бульвар 1-го Салюта, д. 6','','sсhool36l@pochta.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,32,'МОУ СОШ №37','Кузьмина Инна Вячеславовна','35-90-11, 35-90-15','308012, г. Белгород,ул. Привольная, д. 16','','sсhool37@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,33,'МОУ Лицей №38','Войтенко Григорий Петрович','25-95-92, 25-96-93','308012, г. Белгород,бульвар 1-го Салюта, д. 8','','jigalova@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,34,'МОУ СОШ №39','Бочарова Ирина Анатольевна','25-14-10, 25-72-44','308012, г. Белгород,ул. Королева. д. 22','','sсhool39bel@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,35,'МОУ СОШ №40','Калитовская Валентина Матвеевна','52-41-18, 52-41-20','308034, г. Белгород,ул. Шаландина, д.5','','sсhool40bel@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,36,'МОУ СОШ №41','Кириченко Татьяна Дмитриевна','25-82-64, 25-02-90','308012, г. Белгород,Бульвар Юности, д. 4','','Sсhool41@ belgorod.edu.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,37,'МОУ СОШ №42','Тамбовцев Геннадий Алексеевич','51-01-56, 51-85-23','308036, г. Белгород,ул. 60 - летия Октября, д. 7','','Shool42@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,38,'МОУ СОШ №43','Родченко Лариса Анатольевна','51-23-73, 51-82-87, 51-32-56','308036, г. Белгород,ул. 60 - летия Октября, д. 4','www.43.belgorod.ru','Sсhool43@belgorod.edu.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,39,'МОУ СОШ №45','Бугаева Людмила Ивановна','32-03-46, 32-98-48, 32-36-71','308015, г. Белгород,проспект Славы, д. 69','','Sсhool45 belgorod@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,40,'МОУ СОШ №46','Крытченко Ольга Федоровна','51-81-06','308036, г. Белгород,ул. Спортивная, д. 6-А,','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,41,'МОУ СОШ №47','Пеньков Владимир Евгеньевич','35-19-81','308027, г. Белгород,ул. Дегтярева, д.1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,42,'МОУ СОШ №48','Виноградская Марина Викторовна','32-63-89, 32-63-90','326389, г. Белгород,ул. Октябрьская, д. 59-А','','belsсhool48@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,43,'МОУ СОШ №49','Ламанов Владимир Андреевич ','53-54-84, 53-53-31','308036, г. Белгород,ул. Конева, д. 11','','sсhool49@belnet.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,44,'Негосударственное (религиозно- общественное) образовательное учреждение <Православная гимназия во имя святых Мефодия и Кирилла>','Виноградова Елена Валентиновна','26-44-78, 31-05-37, Ф:31-05-37','308002, г. Белгород,1-ый Заводской пер., д. 12','www.Blagovest.bel.ru','gim_bel&mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,45,'Негосударственное образовательное учреждение <СОШ Искорка>','Ладошкина Ольга Николаевна','54-58-07, 25-43-81, 25-24-12','308024, г. Белгород,ул. Костюкова, д. 27','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,46,'Белгородский областной лицей милиции  им. Героя России В.В. Бурцева','Калашников Александр Михайлович','25-13-55, Ф:25-90-52','308024, г. Белгород,ул. Горького, д. 61-Б','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (21,47,'Государственное образовательное учреждение школа-интернатБелгородский лицей- интернат №25','Рухленко Николай Михайлович','26-78-90, 26-78-77','308007, г. Белгород,ул. Гагарина, д.2','','sсhool_25@belnet.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,1,'МОУ Разуменская СОШ №1','Тупикина Надежда Ивановна','39-41-84','308510 п.Разумное, ул.Бельгина, 14','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,2,'МОУ Разуменская СОШ №2','Манохина Лидия Григорьевна','39-31-06','308510 п.Разумное, ул.Филиппова, 2','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,3,'МОУ Разуменская СОШ №3','Бозина Наталья Алексеевна','37-89-39','308510 п.Разумное, ул.Школьная, 1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,4,'МОУ Беловская СОШ','Цыбина Любовь Николаевна','38-45-19','308517, с.Беловское, ул.Центральная, 33','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,5,'МОУ  Беломестненская СОШ','Белоусов Александр Егорович','38-41-71','308570 с.Беломестное, ул.Центральная, 52','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,6,'МОУ Бессоновская СОШ','Лазарева Валентина Николаевна','38-91-14','308815, с.Бессоновка ул.Мичурина, 4','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,7,'МОУ Ближнеигуменская СОШ','Гребенников Юрий Юрьевич','38-48-39','308515, с.Ближняя Игуменка, ул.Центральная','','Igum31@rambler.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,8,'МОУ Веселолопанская СОШ','Корякин Александр Сергеевич','38-22-91','308580, с.Веселая Лопань, ул.Гагарина, 7','','vlsh@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,9,'МОУ Головинская СОШ','Еременко Александр Викторович','38-53-25','308584, с.Головино, ул.Центральная,1','','as5711@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,10,'МОУ Дубовская СОШ','Шатило Валентина Владимировна','39-89-16','308501, п.Дубовое, ул.Ягодная,3','','398916@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,11,'МОУ Журавлевская СОШ','Бреславец Татьяна Семёновна','38-08-22','308594, с.Журавлевка, ул.Коммунистическая,3','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,12,'МОУ Комсомольская СОШ','Потапов Виктор Иванович','37-72-86','308514, п.Комсомольский , ул.Гайдара,1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,13,'МОУ Крутоложская СОШ','Краснова Наталья Юрьевна','38-59-38','308541, с.Крутой Лог, ул.Ленина, 6','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,14,'МОУ Краснооктябрьская СОШ','Бородина Людмила Андреевна','39-55-49','308592, с.Красный Октябрь, ул.Школьная, 1','','Kroktsh1@belnet.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,15,'МОУ Майская гимназия','Манохин Александр Николаевич','39-24-40','308503, п.Майский, ул.Кирова,16-б','','Maysh1@belnet.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,16,'МОУ Мясоедовская СОШ','Волобуева Светлана Анатольевна','38-44-19','308516, с.Мясоедово, ул.Трунова, 1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,17,'МОУ Никольская СОШ','Болтенкова Людмила Михайловна','39-72-59','308505, с.Никольское, ул.Школьная','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,18,'МОУ Новосадовская СОШ','Калугин Николай Федорович','38-47-80','308518, п.Новосадовый, Павлова, 15','','kalugin@begtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,19,'МОУ Октябрьская СОШ','Черендина Людмила Васильевна','39-53-99','3085090, п.Октябрьский, ул.Чкалова, 31','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,20,'МОУ Пушкарская СОШ','Смольякова Валентина Ивановна','38-56-80','308513, с.Пушкарное, ул.Центральная,13','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,21,'МОУ Солохинская СОШ','Сегодин Александр Владимирович','38-03-46','308583, с.Солохи, ул.Школьная,1-а','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,22,'МОУ Стрелецкая СОШ','И.о Власова Елена Михайловна','','с.Стрелецкое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,23,'МОУ Тавровская СОШ','Плутахина Зинаида Егоровна','38-55-73','308504, с.Таврово, ул. Садовая, 35','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,24,'МОУ Хохловская СОШ','Шандура Людмила Федоровна','38-41-85','308572, с.Хохлово, ул.Центральная','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,25,'МОУ Щетиновская СОШ','Сыроватченко Татьяна Викторовна','38-01-37','308562, с.Щетиновка, ул.Молодёжная,1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,26,'МОУ Яснозоренская СОШ','Карнасюк Сергей Александрович','38-33-43','308507, с. Ясные Зори, ул.Школьная,1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,27,'МОУ Северная СОШ №1','Чернышев Валерий Васильевич','39-96-25','308519, п.Северный, ул.Школьная,1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (2,28,'МОУ Северная СОШ №2','Базаров Николай Иванович','39-90-74, Ф:39-90-09','308519, п.Северный, ул.Олимпийская,19','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (3,1,'МОУ СОШ Борисовская № 1','Харитченко Людмила Андреевна','(8-246)5-10-27','309340, пос.Борисовка, ул.Советская, 1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (3,2,'МОУ СОШ Борисовская № 2','Сиротенко Мария Петровна','(8-246)5-12-36','309340, пос.Борисовка, ул.Советская, 67','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (3,3,'МОУ Борисовская СОШ им.С.М.Кирова','Литвин Алла Николаевна','(8-246)5-18-87','309340,п.Борисовка, ул.Республиканская, 40','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (3,4,'МОУ Борисовская СОШ № 4','Трегубенко Светлана Петровна','(8-246)5-30-97','309340, п.Борисовка, ул.Грайворонская, 225','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (3,5,'МОУ Грузсчанская СОШ ','Назаренко Вера Александровна','(8-246)59-4-32','309366, Борисовский р-н, с.Грузское','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (3,6,'МОУ Крюковская СОШ ','Колесник Алексей Тихонович','(8-246)59-6-25','309361, Борисовский р-н, с.Крюково','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (3,7,'МОУ Новоборисовская СОШ им.А.В.Сырового','ВасильченкоЕлена Алексеевна','(8-246)5-14-96','309340, Борисовский р-н, с.Беленькое ','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (3,8,'МОУ Октябрьскоготнянская СОШ ','Мирошниченко Татьяна Петровна','(8-246)25-1-48','309357, Борисовский р-н, с.Октябрьская Готня ','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (3,9,'МОУ Стригуновская СОШ ','Твердохлеб Ольга Васильевна','(8-246)56-1-24','309351, Борисовский р-н, с.Стригуны','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (3,10,'МОУ Хотмыжская СОШ ','Почапская Ольга Александровна','(8-246)24-1-59','309360, Борисовский р-н, с.Хотмыжск ','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (3,11,'Проф. лицей № 29 пос.Борисовка','Долина Николай Александрович','(8-246)5-07-04','309340, п.Борисовка, ул.Коминтерна, 16','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,1,'МОУ СОШ №1, г.Валуйки','Дуброва Ирина Вячеславовна','(8-236)3-18-86','309990, г.Валуйки, ул.С.Разина,10','','Valsoch1@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,2,'МОУ Гимназия № 1, г.Валуйки','Слюсарь Александр Петрович','(8-236)3-33-83','309990, г.Валуйки, ул. 1 мая,51','','school_gimn@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,3,'МОУ СОШ №3, г.Валуйки','Звегинцева Наталья Николаевна','(8-236)3-06-38','309990, г.Валуйки, ул.Комсомоьская,28','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,4,'МОУ СОШ №4, г.Валуйки ','Зеленская Галина Викторовна','(8-236)5-54-15','309990, г.Валуйки,ул. Котовского,16','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,5,'МОУ СОШ №5, г.Валуйки','Грабовский Николай Антонович','(8-236)54-1-43','309990, г.Валуйки, ул. Фурманова,28','','soch5@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,6,'МОУ СОШ Борчанская','Завгородняя Валентина Федоровна','(8-0225)9-64-47','309972 с.Борки','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,7,'МОУ Бутырская СОШ ','Мирошниченко Валентина Петровна','(8-0225)9-27-21','309956, с.Бутырки','','Mirovalentina5@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,8,'МОУ Герасимовская СОШ ','ПриходькоЮрий Иванович','(8-0225)9-64-47','309977, с.Герасимовка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,9,'МОУ Двулученская СОШ ','Щелычев Федор Алексеевич','(8-0225)7-56-70','309975, с.Двулучное','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,10,'МОУ Казинская СОШ ','Попов Василий Григорьевич','(8-0225)9-55-51','309966, с.Казинка','','Kasink_ school@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,11,'МОУ Казначеевская СОШ ','Ободенко Петр Евгеньевич','(8-0225)9-54-20','309967, с.Казначеевка','','koosch@mail.ru, seweka@rambler.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,12,'МОУ Колосовская СОШ ','Никонова Александр Стапанович','(8-0225)9-81-56','309965,с.Колосково','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,13,'МОУ Мандровская СОШ ','Обищенко Тамара Ртифоновна','(8-0225)9-23-13','309950, с.Мандрово','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,14,'МОУ Насоновская СОШ ','Сорокина Нина Алексанровна','(8-0225)9-61-23','309960, с.Насонова','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,15,'МОУ Новопетровская СОШ ','Посохова Зоя Петровна','(8-0225)2-75-99','309973, с.Новопетровка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,16,'МОУ Принцевская СОШ ','Балан Александр Сергеевич','(8-0225)9-13-31','309980,с.Принцевка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,17,'МОУ Рождественская СОШ ','Антоненко Иван Иванович','(8-0225)9-21-28','309954, с.Рождественно','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,18,'МОУ Селивановская СОШ ','Ерёмина Зинаида Егоровна','(8-0225)9-17-17','309961, с.Селиваново','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,19,'МОУ Тимоновская СОШ ','Духин Александр Николаевич','(8-0225)9-51-34','309962, с.Тимоново','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,20,'МОУ Уразовская СОШ№1 им. Ф.Энгельса','Черняева Валентина Евдокимовна','(8-0225)2-11-08','309970, п.Уразово, ул.Калинина, 45','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,21,'МОУ Уразовская СОШ№2','Качурин Александр Васильевич','(8-0225)2-13-67','309970, п.Уразово, ул.Пролетарская,18','','Urazovo_ school_2@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (4,22,'МОУ Шелаевская СОШ ','Подерягин Васильевич Савельевич','(8-0225)9-33-55,(8-0225)9-33-91','309974, с. Шелаево','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,1,'МОУ Белоколодезская СОШ ','Бузина Нина Кузьминична','(8-237)56-5-73,(8-237)56-5-34','309726, с.Белый Колодезь','','kolodez@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,2,'МОУ Большелипяговская СОШ ','Веригина Александра Емельяновна','(8-237)48-4-00,(8-237)48-4-10','309722,с.Большие Липяги','','bolip@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,3,'МОУ Вейделевская СОШ ','Гордиено Галина Федоровна','(8-237)5-51-52,(8-237)5-54-98','309720,п.Вейделевка, ул.Центральная 30','','Vcooll37@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,4,'МОУ Викторопольская СОШ ','Кулик Нина Ивановна','(8-237)51-1-37,(8-237)51-4-43','309724,п.Викторополь','','viktoropol@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,5,'МОУ Должанская СОШ им. А. А. Деменьтьева','Липовцева Надежда Михайловна','(8-237)53-5-42','309738,с.Долгое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,6,'МОУ Дегтяренская СОШ ','Шулева Антонина Николаевна','(8-237)54-2-72','309735, с.Дегтярное','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,7,'МОУ Закутчанская СОШ ','Денисенко Надежда Ивановна','(8-237)52-1-73','309731, с.Закутское','','Zak@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,8,'МОУ Зенинская СОШ ','Халтурина Валентина Николаевна','(8-237)42-3-98','309729, с.Зенино','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,9,'МОУ Клименковская СОШ ','Резниченко Петр Сергеевич','(8-237)47-5-10,(8-237)47-5-43','309725, с.Клименки','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,10,'МОУ Колесняковская СОШ ','Красноперов Владимир Иванович','(8-237)41-3-33','309728, с.Колесняки','','ksh@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,11,'МОУ Малакеевская СОШ ','Федурина Валентина Егоровна','(8-237)44-4-23,(8-237)44- 4-48','309736, с.Малакеево','','Mal999@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,12,'МОУ Николаевская СОШ ','Мысливец Галина Ивановна','(8-237)45-1-25,(8-237)45-2-26','309733, с.Николаевка','','admruk@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,13,'МОУ Ровновская СОШ ','Сиротенко Ольга Александровна','(8-237)46-1-37','309734, с.Ровны','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (5,14,'МОУ Солонцинская СОШ ','Шелудченко Григорий Мифодьевич','(8-237)49-3-25,(8-237)49-4-24','309727, с.Солонцы','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,1,'МОУ Волоконовская СОШ №1','Горюнова Алла Геннадьевна','(8-235)5-13-56','309650,ул. Пионерская, 20','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,2,'МОУ Волоконовская СОШ №2','Ерзов Владимир Сергеевич','(8-235)5-06-06','309650,ул. Коммунистическая,2','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,3,'МОУ Борисовская СОШ ','Горлачев Александр Иванович','(8-235)4-55-62','309675, с.Борисовка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,4,'МОУ Волчье-Александровская СОШ ','Кононенко Лидия Ивановна','(8-235)5-05-12','309672, с.Волчья Александровка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,5,'МОУ Грушевская СОШ ','Рязанова Елена Михайловна','(8-235)4-75-32','309674, с.Грушевка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,6,'МОУ Погромская СОШ ','Андрющенко Сергей Иванович','(8-235)4-66-11','309666, с.Погромец','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,7,'МОУ Покровская СОШ ','Ильченко Сергей Петрович','(8-235)4-11-65','309661, с.Покровка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,8,'МОУ Пятницкая СОШ ','Нагорский Геннадий Анатольевич','(8-235)5-62-53','309655, п.Пятницкое,ул. Маресевой,7','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,9,'МОУ Староивановская СОШ ','Хихлушка Виталий Николаевич','(8-235)4-84-93','309667, с.Староивановка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,10,'МОУ Репьвская СОШ им.Винокурова Ф.И.','Уланов Сергей Владимирович','(8-235)5-82-21','309663, с.Репьевка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,11,'МОУ Тишанская СОШ ','Фролова Наталья Петровна','(8-235)5-84-46','309676, с.Тишанка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,12,'МОУ Фощеватовская СОШ ','Филиппова Надежда Антоновна','(8-235)4-94-41','309664, с.Фощеватова','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,13,'МОУ Шидловская СОШ ','Кравченко Сергей Иванович','(8-235)4-33-42','309671, с.Шидловка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,14,'МОУ Ютановская СОШ ','Жменя Александр Анатольевич','(8-235)4-22-97','309670, с.Ютановка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (6,15,'МОУ Пятницкая СОШ (интернат)','Крахмаль Пётр Никитович','(8-235)5-63-50','309665, п.Пятницкое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,1,'МОУ Гимназия № 1','Шевченко Валентина Викторовна','(8-261)4-54-30,(8-261)4-53-30','309370, г. Грайворон,ул. Горького, д.2','','gimnazj_graj@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,2,'МОУ Грайворонская СОШ им. В.Г. Шухова','ТолмачевСергей Анатольевич','(8-261)4-55-43','309370, г. Грайворон, ул. Мира, 41','','schuhova_gr@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,3,' МОУ Головчинская СОШ с углубленным изучением отдельных предметов','Понеделко Николай Павлович','(8-261)3-52-72,(8-261)3-53-58,(8-261)3-51-57','309376, Грайворонский район, с. Головчино,ул, Смирнова, д.2','','golovchino@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,4,'МОУ Гора-Подольская СОШ ','Колесников Анатолий Геннадьевич ','(8-261)4-64-48','309382, Грайворонский район, с. Гора-Подол, ул. Борисенко','','g-podol_graj@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,5,'МОУ Дорогощанская СОШ ','Игнатенко Вера Ивановна','(8-261)4-11-90','309390, Грайворонский район, с. Дорогощь, ул. Первомайская','','dorog_gr@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,6,'МОУ Дунайская  СОШ им. А.Я. Волобуева','Маринина Елена Федоровна','(8-261)4-31-47','309391, Грайворонский район, с. Дунайка, ул.Школьная, д. 19','','dunayca@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,7,'МОУ Ивано-Лисичанская СОШ ','Галайко Иван Николаевич','(8-261)4-81-12','309397, Грайворонский район, с.Ивановская Лисица, ул. Комсомольская, 24','','ivanolisiz@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,8,'МОУ Козинская СОШ ','Бирюков Виктор Иванович','(8-261)4-75-23','309384, Грайворонский район, с. Козинка, ул. Центральная, д.18','','kozinca@belgtts.ru ');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,9,'МОУ Безыменская СОШ ','Гомон Павел Алексеевич','(8-261)4-77-77','309381, Грайворонский район, с. Безымено, ул. Октябрьская','','bezimeno@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,10,'МОУ Почаевская СОШ  ','Смогарёва Надежда Владимировна ','(8-261)4-91-49','309395, Грайворонский район, с. Почаево','','pochaevo@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,11,'МОУ Смородинская  СОШ ','Смородинова Валентина Ивановна ','(8-261)4-21-47','309394, Грайворонский район, с. Смородино','','smorodino@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,12,'МОУ Замостянская СОШ  ','Петров Николай Климентьевич','(8-261)4-53-83','309370, Грайворонский район, с. Замостье, ул. Добросельская ','','zamostye@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,13,'МОУ Горьковская СОШ  ','Линник Юрий Иванович','(8-261)3-54-91','309387, Грайворонский район, пос. Горьковский, ул. Молодежная, д.2 ','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,14,'МОУ Мокро-Орловская СОШ ','Бакшеева Людмила Григорьевна','(8-261)6-41-17','309392, Грайворонский район, с. Мокрая Орловка, ул. Центральная, д.45','','orlovka_gr@belgtts.ru ');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (7,15,'СОШ № 155 при в/ч 25624','Мозговой Валентин Федорович','','Белгород-22, в/ч 25624','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,1,'МОУ СОШ №1, г.Шебекино','Подколзина Мария Петровна','(8-248)4-19-46,(8-248)4-18-03,(8-248)4-22-78','309290, г.Шебекино, ул.Мичурина,2','www.bel.edu.ru/shebekino/shcool1','shcoolone@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,2,'МОУ СОШ №2, г.Шебекино','Передеряева Нина Васильевна','(8-248)3-01-99,(8-248)3-09-82,(8-248)3-04-81','309290, г.Шебекино, ул.Садовая,7','www.bel.edu.ru/shebekino/shcool2','SHEBSCH2@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,3,'МОУ СОШ №3, г.Шебекино','Шкрогаль Татьяна Васильевна','(8-248)4-82-03, (8-248)4-80-09, (8-248)4-80-92,(8-248)4-82-52','309291, г.Шебекино, ул.Октябрьская,3','www.bel.edu.ru/shebekino/shcool3','shcool3@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,4,'МОУ СОШ №4, , г.Шебекино','Тучкова Ольга Викторовна','(8-248)4-25-82, (8-248) 4-26-39','309290, г.Шебекино, ул.Ленина,19','www.bel.edu.ru/shebekino/shcool4','detris@ shebekino.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,5,'МОУ СОШ №5, г.Шебекино','Воротеляк Валентина Степановна','(8-248)2-74-00,(8-248)2-72-07','309290, г.Шебекино, ул.Дзержинского,18','www.bel.edu.ru/shebekino/shcool5','SHCOOL5@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,6,'МОУ СОШ №6, г.Шебекино','Байдина Людмила Николаевна','(8-248)3-11-44, (8-248) 3-19-38','309250, г.Шебекино, ул.Ржевское шоссе,233','www.bel.edu.ru/shebekino/shcool6','sonbel@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,7,'ГО Шебекинская гимназия-интернат','Купина Татьяна Ивановна','(8-248)2-71-39, (8-248) 2-88-85','309292, г.Шебекино, ул.Ленина,91-а','','LADA2000@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,8,'МОУ Булановская  СОШ','Бавыкина Раиса Андреевна','(8-248)66-5-64','309284, Шебекинский р-он, с.Булановка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,9,'МОУ Белоколодезянская СОШ','Селезнев Василий Николаевич','(8-248)69-5-58','309285, Шебекинский р-он, с.Белый Колодезь, ул.Пионерская','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,10,'МОУ Белянская СОШ','Приходько Татьяна Ивановна','(8-248)77-5-24','309273, Шебекинский р-он, с.Белянка, ул.Школьная','www.bel.edu.ru/shebekino/belyanka','BELJNKA_SH@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,11,'МОУ Большетроицкая СОШ','Тихонова Зоя Ильинична','(8-248)62-4-43, (8-248) 62-2-48','309280, Шебекинский р-он, с. Большетроицкое, пер.Чапаева,11','BTrschool@ belgtts.ru','www.bel.edu.ru/shebekino/shcool/big3');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,12,'МОУ Большегородищенская СОШ','Зыбин Анатолий Иванович','(8-248)78-5-47','309265, Шебекинский р-он, с.Большое Городище, ул.Советская, 3','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,13,'МОУ Верхнеберезовская СОШ','Гребенюк Александр Иванович','(8-248)61-2-64','309265, Шебекинский р-он, с.Верхнее Берёзово, ул.Кооперптивная,1','','Vbschool@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,14,'МОУ Вознесеновская СОШ','Петренко Любовь Григорьевна','(8-248)75-4-65','309259, Шебекинский р-он, с.Вознесеновка, ул.Бутырина,1','','Voznesenovka@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,15,'МОУ Графовская СОШ','Свинарева Ирина Александровна','(8-248)71-3-22, (8-248)71-3-10','309277, Шебекинский р-он, с.Графовка, ул.Центральная, 1а','','GRAF4@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,16,'МОУ Кошлаковская СОШ','Тарасова Ольга Викторовна','(8-248)74-6-86, (8-248)74-6-87','309252, , Шебекинский р-он, с.Кошлаково','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,17,'МОУ Купинская СОШ','Соколов Петр Евдокимович','(8-248)78-4-53, (8-248)78-4-52','309263, Шебекинский р-он, с.Купина','','SCHOOL_KUP@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,18,'МОУ Красненская СОШ','Куликова Любовь Петровна','(8-248)74-9-90, (8-248)74-3-99','309271, Шебекинский р-он, с.Красное, ул.Школьная','','School-KL@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,19,'МОУ Краснополянская СОШ','Мещерякова Анна Яковлевна','(8-248)64-5-14','309288, Шебекинский р-он, с.Красная Поляна, ул.Гагарина','','LysakVA@ yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,20,'МОУ Максимовская СОШ','Леонов Анатолий Иванович','(8-248)61-5-98','309281, Шебекинский р-он, с.Максимовка','','Maksschool@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,21,'МОУ Мешковская  СОШ','Скрыпникова Татьяна Анатольевна ','(8-248)60-5-43','309282, Шебекинский р-он, с.Мешковое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,22,'МОУ Масловопристанская СОШ','Сивых Елена Ивановна','(8-248)55-5-30,(8-248)55-3-92,(8-248)55-5-54','309276, Шебекинский р-он, п.Маслова Пристань, ул.Шумилова,1','www.bel.edu.ru/shebekino/shcool/maslo','mpsch@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,23,'МОУ Муромская СОШ','Ходеева Елена Витальевна','(8-248)79-5-49','309257, Шебекинский р-он, с.Мурово, ул.Гагарина','www.bel.edu.ru/shebekino/shcool/murom','School-M@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,24,'МОУ Новотаволжанская СОШ','Кириевский Станислав Митрофанович','(8-248)73-5-30,(8-248)73-5-71','309255, Шебекинский р-он, с. Новая Таволжанка,','www.bel.edu.ru/shebekino/shcool/newtov','NTSCHOOL @ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,25,'МОУ Поповская СОШ','Белоусова Елена Николаевна','(8-248)65-5-00','Шебекинский р-он, с.Поповка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,26,'МОУ Первоцеплявская СОШ','Тимофеева Елена Ивановна','(8-248)71-5-45,(8-248)71-5-84,(8-248)71-5-21','309247, Шебекинский р-он, с.Первоцепляево,ул.Ленина,1','','SCHOOL-P-C @ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,27,'МОУ Ржевская  СОШ','Разуваев Владимир Пантелеевич','(8-248)70-3-46','309261, Шебекинский р-он, с.Ржевка, ул.Пионерская,49','','Rzevka@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (19,28,'МОУ Стариковская  СОШ','Прокопчук Владимир Сергеевич','(8-248)78-7-35','309264, Шебекинский р-он, с.Стариково, ул.Советская,19','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,1,'МОУ Чернянская  СОШ№1','Цуканова Елена Геннадьевна ','(8-232)5-56-76','п. Чернянка, ул. Революции','','mikrosca@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,2,'МОУ Чернянская  СОШ№2','Андреева Елена Михайловна','(8-232)5-56-73','п. Чернянка,  пл. Октябрьская, 4','','shkola2@belgtts.ru ');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,3,'МОУ Чернянская  СОШ№3','Чуб Марина Владимировна','(8-232)5-53-01','п. Чернянка, ул. Школьная,11','','shkola3@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,4,'МОУ Чернянская  СОШ№4','Кривенко Ольга Анатольевна','(8-232)5-57-95','п. Чернянка,  ул. Кольцова,38','','shkola4@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,5,'МОУ СОШ с. Андреевка','Косинова Елена Александровна','(8-232)3-65-44','Чернянский р-н, с. Андреевка','','sh_andr@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,6,'МОУ СОШ с. Волотово','Стасенко Надежда Валентиновна','(8-232)4-92-23','Чернянский р-н, с. Волотово','','sh_volot@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,7,'МОУ СОШ с. Волоконовка','Туренко Владимир Сергеевич','(8-232)3-41-10','Чернянский р-н, с. Волоконовка','','sh_volok@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,8,'МОУ СОШ с. Волково','Приболовец Адам Павлович','(8-232)4-25-44','Чернянский р-н, с. Волково','','sh_ogib@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,9,'МОУ СОШ с. Ездочное','Воронина Галина Леонидовна','(8-232)4-05-67','Чернянский р-н, с. Ездочное','','ezd@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,10,'МОУ СОШ с. Кузькино','Черкесов Иван Андреевич','(8-232)4-81-35','Чернянский р-н, с. Кузькино','','sh_kuz@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,11,'МОУ СОШ с. Ковылино','Съедин Владимир Федорович','(8-232)3-55-34','Чернянский р-н, с. Ковылино','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,12,'МОУ СОШ с. Кочегуры','Шлыкова Татьяна Алексеевна','(8-232)4-35-37','Чернянский р-н, с. Кочегуры','','kochegur@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,13,'МОУ СОШ с. Лозное','Щепилов Василий Васильевич','(8-232)4-44-93','Чернянский р-н, с. Лозное','','loznoe@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,14,'МОУ СОШ с. Лубяное','Мирошниченко Нина Николаевна','(8-232)4-61-34','Чернянский р-н, с. Лубяное','','sh_lub@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,15,'МОУ СОШ с. Малотроицкое','Мухина Ольга Александровна','(8-232)4-51-35','Чернянский р-н, с. Малотроицкое','','mtroi@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,16,'МОУ СОШ с. Новоречье','Черникова Светлана Васильевна','(8-232)4-71-46','Чернянский р-н, с. Новоречье','','sh_novor@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,17,'МОУ СОШ с. Ольшанка','Пономарева Елена Васильевна','(8-232)3-25-44','Чернянский р-н, с. Ольшанка','','olsh@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,18,'МОУ СОШ с. Орлик','Шаповалов Андрей Владимирович','(8-232)4-15-93','Чернянский р-н, с. Орлик','','sh_orlik@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (18,19,'МОУ СОШ с. Русская Халань','Доманова Галина Валентиновна','(8-232)3-11-24','Чернянский р-н,с. Русская Халань','','sh_xalan@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,1,'МОУ СОШ №1','Капкова Людмила Сергеевна','(8-0225)24-53-76','309504, г.Ст.Оскол, м-н Горняк,35','','st_shl@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,2,'МОУ СОШ №2','Полупанова Лидия Ивановна','(8-0225)227635','309506, г.Ст.Оскол, м-н Углы,17','','st_sh2@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,3,'МОУ Лицей №3','Лаптева Надежда Петровна','(8-0225)24-52-56, (8-0225)24-77-59ф: (8-0225) 245256','309530, г.Ст.Оскол, м-н Интернациональный,1','','st_l13@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,4,'МОУ СОШ №5 с углубленным изучением отдельных предметов','Вялкова Людмила Ивановна','(8-0225)22-07-12?(8-0225)22-42-45','309530, г.Ст.Оскол, ул.Октябрьская,10','','st_ sh5@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,5,'МОУ СОШ №6 ','Пьяных Мария Васильевна','','309512, г.Ст.Оскол, м-н Жукова,36','','st_sh6@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,6,'МОУ СОШ №8','Виноградская Лариса Борисовна','(8-0225)22-48-92,(8-0225)22-05-47','309514, г.Ст.Оскол, ул.Пролетарская,72а','','st_sh8@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,7,'МОУ СОШ №11','Дзюба Елена Петровна','(8-0225)24-31-40,(8-0225)24-75-78','309530, г.Ст.Оскол, м-н Интернациональный,23','','stsh11@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,8,'МОУ СОШ №12 с углубленным изучением отдельных предметов','Чаплыгина Татьяна Алексеевна','(8-0225)24-52-41','309530, г.Ст.Оскол, м-н Лебединец,28','','st_sh12@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,9,'МОУ СОШ №13','Котарева Наталья Ивановна','(8-0225)24-16-40','309530, г.Ст.Оскол, м-н Парковая,27','','st_sh13@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,10,'МОУ СОШ №14','Лебедева Людмила Анатольевна','(8-0225)25-56-29','309518, г.Ст.Оскол, м-н Приборостроитель,16','','st_sh14@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,11,'МОУ СОШ №15','Москаленко Валентина Николаевна','(8-0225)24-01-01','309530, г.Ст.Оскол, м-н Молодогвардеец,15','','st_sh12@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,12,'МОУ СОШ №16 с углубленным изучением отдельных предметов','Колесник Нина Ивановна','(8-0225)32-19-56',' 309512, г.Ст.Оскол, м-н Жукова,56','','st_sh16@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,13,'МОУ СОШ №17','Буталий Любовь Николаевна','','309512, г.Ст.Оскол, м-н Жукова,57','','Stsh17@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,14,'МОУ Гимназия № 18','Демидова Вера Донадовна','','309511, г.Ст.Оскол, м-н Олимпийский,8','www.gimnazia.narod.ru','st_sh18@belgtts.ru ');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,15,'МОУ СОШ №19 с углубленным изучением отдельных предметов','Иванова Елена Ивановна','(8-0225)24-42-51','309517, г.Ст.Оскол, м-н Рудничный, 22','','st_sh19@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,16,'МОУ СОШ №20 с углубленным изучением отдельных предметов','Кулакова Людмила Юрьевна','(8-0225)32-16-02, (8-0225)32-80-77Ф: (8-0225)42-48-45','309511, г.Ст.Оскол, м-н Олимпийский,54','','st_sh20@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,17,'МОУ СОШ №21','Костина Ирина Валентиновна','','309530, г.Ст.Оскол, м-н Юность,9','http://www.sh-21. narod.ru','st_sh21@belgtts.ru ');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,18,'МОУ СОШ №22 с углубленным изучением отдельных предметов','Щукин Алексей Васильевич','(8-0225)32-46-09','309511, г.Ст.Оскол, м-н Олимпийский,34','','st_sh22@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,19,'МОУ СОШ №24 с углубленным изучением отдельных предметов','Лытынина Ольга Ивановна','(8-0225)32-12-37','309512, г.Ст.Оскол, м-н Конева,15а','','st_sh24@belgtts.ru ');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,20,'МОУ СОШ №25','Межуев Валентин Семёнович','','309530, г.Ст.Оскол, м-н Буденного,2','','st_sh25@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,21,'МОУ СОШ № 26 с углубленным изучением отдельных предметов','Дубникова Тамара Петровна','(8-0225)32-47-42','309530, г.Ст.Оскол, м-н Солнечный,19','','stsh26@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,22,'МОУ СОШ №27 с углубленным изучением отдельных предметов','Часовских Тамара Яковлевна','(8-0225)25-31-79','309530, г.Ст.Оскол, м-н Весенний','www.oss27.narod.ru','st_sh27@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,23,'МОУ СОШ №28 с углубленным изучением отдельных предметов','Марчукова Галина Викторовна','(8-0225)32-46-37','309516, г.Ст.Оскол, м-н Макаренко,36а','','st_sh28@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,24,'МОУ СОШ №30','Трубина Лариса Азизовна','(8-0225)33-12-11','309502, г.Ст.Оскол, м-н Королева,17','','st_sh30@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,25,'МОУ СОШ №33','Бредихин Александр Николаевич','(8-0225)43-04-41','309502, г.Ст.Оскол, м-н Юбилейный,10','','st_sh33@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,26,'МОУ СОШ №34','Телицын Владимир Петрович','(8-0225)33-74-33','309502, г.Ст.Оскол, м-н Королева,16','','st_sh34@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,27,'МОУ открытая (сменная) общеобразовательная школа №35','ЧеканцеваВалентина Алексеевна','(8-0225)22-06-47,(8-0225)22-69-85','309530, г.Ст.Оскол, ул.Комсомольская,33/36','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,28,'МОУ СОШ №36','Шлейдер Вера Михайловна','(8-0225)36-33-26','309508, г.Ст.Оскол, ул.Стадионная,14','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,29,'МОУ СОШ №40','Филимонова Анна Гавриловна','(8-0225)42-85-90','309530, г.Ст.Оскол, м-н Восточный, 51','','st_sh40@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,30,'МОУ Архангельская СОШ ','Емельянова Нина Ивановна','(8-0225)39-31-46','309544, Старооскольский р-он, с.Архангельское','','st_arh@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,31,'МОУ Верхнечуфическая СОШ ','Самофалова Татьяна Юрьевна','(8-0225)39-67-46','309531,Старооскольский р-он,с.Верхнее Чуфичево','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,32,'МОУ Владимировская СОШ ','Шевченко Антонина Сергеевна','(8-0225)39-73-38','309553, Старооскольский р-он, с.Владимировка','','st_vlad@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,33,'МОУ Городищенская СОШ с углубленным изучением отдельных предметов','Курчина Валентина Дмитриевна','(8-0225)39-76-47','309546, Старооскольский р-он, с. Городище, ул. Гагарина,1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,34,'МОУ Дмитриевская СОШ ','Карпинская Надежда Дмитриевна','(8-0225)39-02-43, (8-0225) 39-02-81','309549, Старооскольский р-он, с.Дмитриевка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,35,'МОУ Знаменская СОШ ','Крамаренко Наталья Павловна','(8-0225)39-61-82','309555, Старооскольский р-он, с.Знаменка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,36,'МОУ Ивановская СОШ ','Малюков Владимир Митрофанович','(8-0225)39-65-10','309528, Старооскольский р-он, с.Ивановка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,37,'МОУ Каплинская СОШ с углубленным изучением отдельных предметов','Дорохин Иван Николаевич','(8-0225)22-34-51, (8-0225)22-34-11','309536, Старооскольский р-он, с.Федосеевка, ул.Н.Л.ихачева,47','','st_kapl@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,38,'МОУ Курская СОШ ','Пирогова Анна Александровна','(8-0225)39-23-37','309535, Старооскольский р-он, с.Лапыгино','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,39,'МОУ Крутовская СОШ ','Мыцин Геннадий Павлович','(8-0225)39-41-37','309554, Старооскольский р-он, с.Крутое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,40,'МОУ Котовская СОШ ','Коршикова Анна Митрофановна','(8-0225)39-21-35','309541, Старооскольский р-он, с.Котово','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,41,'МОУ Монаковская СОШ ','Кривошеева Мария Дмитриевна','(8-0225)39-63-35','309532, Старооскольский р-он, с.Монаково','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,42,'МОУ Обуховская СОШ ','Романченко Нина Ивановна','(8-0225)37-43-33','309545, Старооскольский р-он, с.Обуховка, ул.Школьная,30','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,43,'МОУ Озёрская СОШ ','Ищенко Татьяна Алексеевна','(8-0225)39-71-43','309543, Старооскольский р-он, с.Озёрки','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,44,'МОУ Песчанская СОШ ','Ченцова Татьяна Ивановна','(8-0225)39-51-31','309539, Старооскольский р-он, с.Песчанка','','st_pesh@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,45,'МОУ Потудановская СОШ ','Корнева Надежда Михайловна','(8-0225)39-33-38','309556, Старооскольский р-он, с.Потудань','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,46,'МОУ Рогаватовская СОШ с углубленным изучением отдельных предметов','Юдин Иван Дмитриевич','(8-0225)39-06-89','309551, Старооскольский р-он, с.Роговатое','','st_rogov@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,47,'МОУ Солдатовская СОШ ','Ерёмин Александр Валентинович','(8-0225)39-44-58','309548,Старооскольский р-он, с.Солдатское','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,48,'МОУ Тереховская СОШ ','Ткаченко Ирина Евгеньевна','(8-0225)39-27-46','309542,Старооскольский р-он, с.Терехово','','st_ter@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (22,49,'МОУ Шаталовская СОШ ','Кондратенко Нина Алексеевна','(8-0225)93-82-47,(8-0225)39-82-39','309550,Старооскольский р-он, с.Шаталовка','','st_shat@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,1,'МОУ Айдарская СОШ ','Становский П.Б.','(8-238)54-3-27','309761, Ровеньской р-он, с.Айдар','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,2,'МОУ Верхнесеребрянская СОШ ','Омелаева Мария Николаевна','(8-238)37-3-31','309742, Ровеньской р-он, с.Верхняя Серебрянка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,3,'МОУ Жабская  СОШ ','Лангавая Вера Митрофановна','(8-238)36-1-33','309757, Ровеньской р-он, с.Жабское','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,4,'МОУ Клименковская СОШ ','Грищенко Николай Александрович','(8-238)51-1-34','309746, Ровеньской р-он, с.Клименково','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,5,'МОУ Ладомировская СОШ ','Иванова Риаса Алексеевна','(8-238)38-6-38','309765, Ровеньской р-он, с.Ладомировка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,6,'МОУ Лознянская СОШ ','Мышанский Николай Васильевич','(8-238)35-2-38','309747, Ровеньской р-он, с.Лозная','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,7,'МОУ Лозовская СОШ ','Клименко Людмила Алексеевна','(8-238)39-5-18','309744, Ровеньской р-он, с.Лозовое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,8,'МОУ Масловская СОШ ','Максименко Раиса Ивановна','(8-238)52-1-88','309758, Ровеньской р-он, с.Масловка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,9,'МОУ Наголенская СОШ ','Бережная Серафима Лукьянова','(8-238)31-1-19,(8-238)31-1-43','309745, Ровеньской р-он, с. Нагольное','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,10,'МОУ Нагорьевская СОШ ','Мандрыкин Владимир Иванович','(8-238)53-2-48,(8-238)53-1-57','309750, Ровеньской р-он, с.Нагорье','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,11,'МОУ Нижнесеребрянская СОШ ','Ярова Светлана Егоровна','(8-238)34-2-46','309761, Ровеньской р-он, с. Нижняя Серебрянка','','rovadmvs@beltts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,12,'МОУ  Новоолександроская СОШ ','Гриева Людмила Петровна','(8-238)32-4-78','309763, Ровеньской р-он, с.Новоолександровка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,13,'МОУ Пристеньская СОШ ','Жиренко Михаил Иванович','(8-238)39-2-13','309762, Ровеньской р-он, с.Пристень','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,14,'МОУ Ржевская СОШ ','Морозова Лидия Ивановна','(8-238)31-4-55','309754, Ровеньской р-он, с.Ржевка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,15,'МОУРовеньская СОШ с углубленным изучением отдельных предметов','Куприева Нина Викторовна','(8-238)5-51-51,(8-238)5-67-41','309740, п.Ровеньки, ул.Ленина,147','','rvsn2@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,16,'МОУ Ровеньская СОШ №2 ','Колтакова Наталья Тихоновна','(8-238)5-53-94,(8-238)5-58-53','309740, п.Ровеньки,ул. Пролетарская,41','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,17,'МОУ Харьковская  СОШ ','Бондарь Валерий Иванович','(8-238)36-1-33','309757,Ровеньской р-он, с.Харьковское','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (17,18,'МОУ Ясеновская  СОШ ','Нудная Раиса Павловна','(8-238)33-3-26','309755,Ровеньской р-он, с.Свистовка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,1,'МОУ Бобравская СОШ ','Забуга Любовь  Дмитриевна','(8-245)53-1-46','309317, Ракитянский р-он, с. Бобра, ул. Центральная, 62','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,2,'МОУ Введено-Готнянская  СОШ ','Оксененко Зоя Ивановна','(8-245)28-1-38','309323,Ракитянский р-он,с. Введенская - Готня, ул. Гордеевка, 2','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,3,'МОУ Венгеровская СОШ ','Трунов Виктор Васильевич ','(8-245)51-1-97','309313, Ракитянский р-он, с. Венгеровка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,4,'МОУ Вышнепенская СОШ ','Дурманова Татьяна Васильевна','(8-245)63-1-84','309315,Ракитянский р-он, с. Вышние Пены, ул. Центральная, 20','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,5,'МОУ Дмитриевская  СОШ ','БабынинАлександр Иванович','(8-245)22-1-30, (8-245)22-1-38','309322, Ракитянский р-он, с. Дмитриевка, ул. Шатилова, 7','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,6,'МОУ Илек-Кошарская  СОШ ','Артеменко Наталья Петровна','(8-245)2-11-29','309422, Ракитянский р-он, с. Илек-Кошары','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,7,'МОУ Нижнепенская СОШ ','Жильцов Алексей Афанасьевич,','(8-245)23-2-48','309316, Ракитянский р-он, с. Нижние Пены','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,8,'МОУ Пролетарская СОШ ','Шелкова Ольга Александровна','(8-245)35-0-71','309300, Ракитянский р-он,п. Пролетарский,ул Ватутина,2а','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,9,'МОУ Пролетарская СОШ ','Климов Виктор Петрович','(8-245)35-3-85','309300, Ракитянский р-он, п.Пролетарский,ул.Пролетарская, 32','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,10,'МОУ Ракитянская СОШ№1 ','Ткачев Виталий Николаевич','(8-245)55-3-80','309310, Ракитянский р-он, п.Ракитное, ул.Пролетарская, 10','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,11,'МОУ Ракитянская СОШ №2','Вагнер Татьяна Александровна','(8-245)56-9-75','309310, Ракитянский р-он,п. Ракитное, ул. Коммунаров, 30','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,12,'МОУ Сахзаводская СОШ ','Мишенин Анатолий Николаевич','(8-245)52-4-91','309311,Ракитянский р-он, п. Ракитное-1, ул. Федутенко, 2','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (16,13,'МОУ Солдатская СОШ ','Беликов Евгений Андреевич','(8-245)62-7-27','309301,Ракитянский р-он, с. Солдатское, 32','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,1,'МОУ Беленихинская СОШ ','Стародубова Надежда Дмитриевна ','(8-242)42-1-53,(8-242)42-1-69','309030,Прохоровский район, с.Беленихино','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,2,'МОУ Береговская СОШ ','Ионин Николай Афанасьевич ','(8-242)43-2-35','309001,Прохоровский район, с.Береговое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,3,'МОУ Большанская СОШ ','Бельков Леонид Тимофеевич ','(8-242)41-1-89','309023,Прохоровский район, с.Большое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,4,'МОУ Вязовская СОШ ','Гудов Сергей Александрович ','(8-242)28-3-11','309014,Прохоровский район, с.Вязовое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,5,'МОУ Донецкая СОШ ','Ласковец Наталья Васильевна ','(8-242)27-5-35','309027,Прохоровский район, с.Донец','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,6,'МОУ Журавская СОШ ','Шеенко Валентина Борисовна ','(8-242)45-5-41','309014,Прохоровский район, с.Журавка','','zghurschool@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,7,'МОУ Коломыцевская СОШ ','Мирошникова Людмила Степановна ','(8-242)27-5-14','309028,Прохоровский район, с.Коломыцево','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,8,'МОУ Кривошеевская СОШ ','Гребенкина Ольга Федеровна','(8-242)48-5-50','309015,Прохоровский район, с.Кривошеевка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,9,'МОУ Лучковская СОШ ','МарущенкоВиталий Александрович ','(8-242)29-4-43','309032,Прохоровский район, с.Лучки','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,10,'МОУ Масловская СОШ ','Агафонова Любовь Николаевна ','(8-242)26-1-38','309016,Прохоровский район, с.Масловка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,11,'МОУ Маломаяченская СОШ ','Андреева Любовь Николаевна ','(8-242)4-44-87','309031,Прохоровский район, с.Малые Маячки','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,12,'МОУ Подолешенская СОШ ','РакитянскаяЕкатерина Петровна ','(8-242)41-1-39','309022,Прохоровский район, с.Подольхи','','tdauld@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,13,'МОУ Прелестненская СОШ ','Каторгин Вадим Вячеславович ','(8-242)40-5-41','309004,Прохоровский район, с.Прелестное','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,14,'МОУ Призначенская СОШ ','Дедов Василий Яковлевич ','(8-242)40-2-98,(8-242)4-02-99','309020,Прохоровский район, с.Призначное','','dvgeab@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,15,'МОУ Плотавская СОШ ','Маслова Антонина Митрофановна ','(8-242)46-2-97','309038,Прохоровский район, с. Плота','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,16,'МОУ Радьковская СОШ ','Синяков Александр Николаевич ','(8-242)49-3-24','309012,Прохоровский район, с.Радьковка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,17,'МОУ Ржавецкая СОШ ','Гончарова Татьяна Васильевна ','(8-242)49-1-76','309035,Прохоровский район, с.Ржавец','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,18,'МОУ Холоднянская СОШ ','Пиляева Татьяна Ивановна ','(8-242)49-5-49','309026,Прохоровский район, с.Холодное','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,19,'МОУ Шаховская СОШ ','Рязанова Валентина Ивановна ','(8-242)40-3-28','309034,Прохоровский район, с.Шахово','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (15,20,'МОУ Прохоровская гимназия','Новосельцев Виктор Иванович ','(8-242)2-11-52,(8-242)2-15-82','309000, п.Прохоровка,ул.Садовая,2','','prohschool@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,1,'МОУ СОШ № 1 с углубленным изучением отдельных предметов, г. Новый  Оскол','Александрова Людмила Николаевна','(8-233)4-11-61','309642, г. Новый Оскол, ул. Гагарина, 24','','NOSCHOOL1@ BELGTTS.RU');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,2,'МОУ СОШ № 2 с углубленным изучением отдельных предметов, г. Новый  Оскол','Понедельченко Ольга Михайловна','(8-233)4-55-96','309642 г.Новый Оскол, ул.Оскольская, 7','','NOSCHOOL2@ BELGTTS.RU');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,3,'МОУ СОШ № 3, г. Новый  Оскол','Горбатко Лидия Николаевна','(8-233)4-18-73','309640, г. Новый Оскол, ул.Ливенская, 94','','SOSH 3-NO@BELGTT.RU');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,4,'МОУ СОШ № 4, г. Новый Оскол','Тимошенко Валентина Михайловна','(8-233)4-56-68','309641, г. Новый Оскол,ул. Авиационная, 1','','SCOOL №4@ BELGTTS.RU');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,5,'МОУ Львовская СОШ ','Патрахина Анна Васильевна','(8-233)3-21-46','309603 Новооскольский р-он с. Львовка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,6,'МОУ Ольховатская СОШ ','Ивлева Татьяна Митрофановна','(8-233)5-52-53','309607 Новооскольский р-он, с. Ольховатка','','OLHSCOOL@BELGTTS.RU');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,7,'МОУ Старобезгинская СОШ ','Гарнат Александр Владимирович','(8-233)5-91-19','309621 Новооскольский р-он, с. Старая Безгинка','','STBSCHKOLA@BELGTTS.RU');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,8,'МОУ Глинновская СОШ ','Тарасов Вадим Васильевич','(8-233)5-77-48','309614  Новооскольский р-он, с. Глинное','','GLSCOOL@BELGTTS.RU');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,9,'МОУ Голубинская СОШ ','Пугачев Николай Николаевич','(8-233)3-64-16','309616 Новооскольский р-он, с. Голубино','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,10,'МОУ Ярская СОШ ','Величко Зоя Петровна','(8-233)5-81-32','309627 Новооскольский р-он, с. Ярское','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,11,'МОУ Тростенецкая СОШ ','Доронина Александра Васильевна','(8-233)5-31-69','309623 Новооскольский район, с. Тростенец','','SCOOL_TROST@BELGTTS.RU');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,12,'МОУ Шараповская СОШ ','Герасимов Иван Васильевич','(8-233)3-31-72','309610 Новооскольский р-он, с. Шараповка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,13,'МОУ Васильдольская СОШ ','Кононова Валентина Петровна','(8-233)5-41-39','309624 Новооскольский р-он, с. Василь-Дол','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,14,'МОУ Великомихайловская СОШ ','Прядченко Лидия Александровна','(8-233)5-10-24','309620 Новооскольский р-он, с. Великомихайловка','','WMSOCH@RAMBLER.RU');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,15,'МОУ Беломестненская СОШ ','Карташова Людмила Федоровна','(8-233)5-52-87','309609 Новооскольский р-он, с. Беломестное','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,16,'МОУ Борово-Гриневская СОШ ','Аношина Елена Анатольевна','(8-233)3-12-24','309632 Новооскольский р-он, с. Боровки','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (14,17,'МОУ Новобезгинсая СОШ ','Смаженко Зоя Петровна','(8-233)5-73-31','309612 Новооскольский район, с. Новая Безгинка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (11,1,'МОУ Болыповская СОШ им.М.Д.Чубарых','Хантулин Николай Иванович','(8-262)5-72-22','309877, с.Большое, ул.Школьная,1','','krabss@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (11,2,'МОУ Сетищенская СОШ ','Флигинских Тамара Ивановна','(8-262)5-52-44','309888, с.Сетище,ул.Центральная,60','','setish@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (11,3,'МОУ Расховецкая СОШ ','Борисов Василий Иванович','(8-262)5-77-36','309878, с.Расховец, ул.Центральная,28','','raskovecc@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (11,4,'МОУ Новоуколовская СОШ ','Шеншина Наталья Николаевна','(8-262)5-41-72','309875, с.Новоуколово, ул.Школьная,1','','nukolshola@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (11,5,'МОУ Лесноуколовская СОШ ','Манаева Валентина Семеновна','(8-262)5-33-99','309881, с.Лесноуколово, ул.Лесная,35','','lesnskola@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (11,6,'МОУ Кругловская СОШ ','Скворцов Николай Николаевич','(8-262)5-35-22','309873, с.Круглое, ул.Школьная,24','','kruskl@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (11,7,'МОУ Камызинская СОШ ','Ряполова ЛюбовьМихайловна','(8-262)5-82-19','309885, с.Камызино, ул.Маяковского,58','','Kam_sr@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (11,8,'МОУ Красненская СОШ им. М. И. Светличной','Бессмельцева Надежда Павловна','(8-262)5-23-19','309870, с.Красное, ул.Подгорная,1','','bnpss@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (11,9,'МОУ Горская СОШ ','Вальтер Владимир Викторович','(8-262)5-31-34','309882, с.Горки, ул.Центральная,55','','Kra_gorki@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (11,10,'МОУ Готовская СОШ им. А.Н.Маснева','Федяева Татьяна Алексеевна','(8-262)5-36-32','309886, с.Готовье, ул.Центральная,1','','gotovjo@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (13,1,'МОУ Вязовская СОШ ','Евсюкова Валентина Ивановна','(8-263)44-1-44','309430, Краснояружский р-он,с.Вязовое, ул. Первомайская,31','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (13,2,'МОУ Сергеевская СОШ ','Солошина Ольга Николаевна','(8-263)40-1-36','309425, Краснояружский р-он,с.Сергиевка, ул.Центральная,4','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (13,3,'МОУ Илек-Пеньковская СОШ ','Романенко Валентина Семёновна','(8-263)41-5-93','309425, Краснояружский р-он,с. Илек-Пеньковка, ул.Школьная','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (13,4,'МОУ Графовская СОШ ','Попова Анна Александровна','(8-263)48-1-29','309432, Краснояружский р-он,с.Графовка, ул.Центральная,31','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (13,5,'МОУ Степнянская СОШ ','Контаева Валентина Николаевна','(8-263)45-9-34','309423, Краснояружский р-он,с.Степное,ул.Центральная','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (13,6,'МОУ Теребренская СОШ ','Винокурова Наталья Александровна','(8-263)41-1-83','309441, Краснояружский р-он, с.Теребрено, ул.Новостроевка,38','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (13,7,'МОУ Краснояружская СОШ №1','Щербак Ольга Владимировна','(8-263)45-1-69','309420, Краснояружский р-он, п.Красная Яруга, ул.Крыловка,6','','Schol_1@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (13,8,'МОУ Краснояружская СОШ №2','Латыш Екатерина Михайловна','(8-263)45-2-37','309420, Краснояружский р-он, п. Красная Яруга, ул.Мира,1','','jaruga@ mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (13,9,'МОУ Репяховская СОШ ','Шелудченко Елена Николаевна','(8-263)49-6-28','309431, Краснояружский р-он, с.Репяховка, ул.Школьная,9','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,1,'МОУ Арнаутовская СОШ ','Бессмертная Елена Викторовна','(8-247)6-36-42','309944, Красногвардейский р-он, с. Арнаутово,  ул. Заречная, 55','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,2,'МОУ Большебыковская СОШ ','Титова Галина Васильевна','(8-247)6-46-83','309931, Красногвардейский р-он,с. Большебыково,ул. Центральная, 36','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,3,'МОУ Валуйчанская СОШ ','Образцов Алексей Борисович','(8-247)6-84-81','309943, Красногвардейский р-он, с. Валуйчик ','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,4,'МОУ Валуянская СОШ ','Селезнев Алексей Алексеевич','(8-247)6-04-47','309910, Красногвардейский р-он, с. Валуй, ул. Луговая,36','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,5,'МОУ Веселовская СОШ ','Левачкова Наталья Михайловна','(8-247)2-32-72,(8-247)2-34-67','309923, Красногвардейский р-он, с.Веселое, ул. Мира, 160','','veseloesch@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,6,'МОУ Верхососенская СОШ ','Емельяненко Нина Петровн','(8-247)6-74-17','309936, Красногвардейский р-он, с. Верхососна,ул. Центральная, 20','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,7,'МОУ Верхнепокровская СОШ ','Бешенцев Дмитрий Семенович','(8-247)5-51-23,(8-247)3-31-31','309930, Красногвардейский р-он, с. Верхняя Покровка, ул. Советская','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,8,'МОУ Гредякинская СОШ ','Чертовской Александр Алексеевич','(8-247)2-32-50,(8-247)2-34-60','309922, Красногвардейский р-он, с. Гредякино','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,9,'МОУ Засосенская СОШ ','Соколов Александр Степанович','(8-247)3-40-97','309926, Красногвардейский р-он,с. Засосна, ул. Чапаева,1','','zasosnasch@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,10,'МОУ Казацкая СОШ ','Чернякова Зинаида Васильевна','(8-247)6-65-46,(8-247)6-65-40,(8-247)6-65-47','309934, Красногвардейский р-он,с.Казацкое, ул. Дорожная, 1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,11,'МОУ Калиновская СОШ ','Битюцкая Галина Васильевна','(8-247)6-22-88','309912, Красногвардейский р-он, с. Калиново, ул. Центральная, .17','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,12,'МОУ Красногвардейская СОШ ','Черемушкин Игорь Борисович','(8-247)3-29-75,(8-247)3-10-74,(8-247)3-13-73','309920, Красногвардейский р-он, п. Красногвардейское, ул. Красная, 5','','gvaschm@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,13,'МОУ Коломыцевская СОШ ','Тарасова Любовь Васильевна','(8-247)6-04-84','309911, Красногвардейский р-он, с. Коломыцево, ул. Советская, 14','','kolomsch@ belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,14,'МОУ Ливенская СОШ №1','Понамарева Татьяна Юрьевна','(8-247)4-41-97,(8-247)4-42-74','309900, Красногвардейский р-он, с. Ливенка, ул. Учительская, 1','','livenkasch@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,15,'МОУ Ливенская СОШ №2','Дудкин Юрий Николаевич','(8-247)4-42-76,(8-247)4-45-76','309900, Красногвардейский р-он, с. Ливенка, ул. Куйбышева','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,16,'МОУ Марьевская СОШ','Курепин Виктор Александрович','(8-247)6-21-43','309935, Красногвардейский р-он, с. Марьевка, ул. Центральная','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,17,'МОУ Малобыковская СОШ ','Максимов Владимир Павлович','(8-247)6-63-02','309912, Красногвардейский р-он, с. Малобыково, ул. Пушкарная, 3','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,18,'МОУ Новохуторная СОШ ','Свищева Жанна Борисовна','(8-247)6-27-74','309925, Красногвардейский р-он, с. Новохуторное, ул. Молодежная','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,19,'МОУ Никитовская СОШ ','Филипенко Татьяна Митрофановна','(8-247)7-77-59,(8-247)7-78-58','309945, Красногвардейский р-он, с. Никитовка,ул. Калинина','','nikitovkasch@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,20,'МОУ Палатовская СОШ ','Черкасова Антонина Васильевна','(8-247)6-94-30','309942, Красногвардейский р-он, с. Палатово,ул. Набережная, 2','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,21,'МОУ Самаринская СОШ ','Горьянова Нина Михайловна','(8-247)7-76-33,(8-247)7-70-97','309946, Красногвардейский район, с. Самарино, ул. Юбилейная, 1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,22,'МОУ Сорокинская СОШ ','Зинковская Галина Моисеевна','(8-247)5-25-36','309938, Красногвардейский р-он, с. Сорокино, ул. Центральная','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,23,'МОУ Стрелецкая СОШ ','Гондарев Игорь Васильевич','(8-247)6-64-50,(8-247)6-65-80','309934, Красногвардейский р-он, с. Стрелецкое, ул. Победы,1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (12,24,'МОУ Утянская СОШ ','Пищулов Александр Сергеевич','(8-247)6-37-75,(8-247)6-37-74','309932, Красногвардейский р-он, с. Уточка,ул. Советская','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,1,'МОУ Корочанская СОШ ','Жабский Василий Федорович','(8-231)5-59-50','309210, г.Короча, ул.Пролетарская,39','','kor22cu@yndex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,2,'МОУ Алексеевская СОШ ','Савина Галина Михайловна','','309206, Корочанский р-он, с. Алексеевка','','minaioss@beltts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,3,'МОУ Анновская СОШ ','Беспалова Дина Александровна','(8-231)4-11-43','309233, Корочанский р-он, с.Анновка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,4,'МОУ Афанасовская СОШ ','Демченко Галина Николаевна','(8-231)4-57-91','309236, Корочанский р-он, с.Афанасово','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,5,'МОУ Бехтеевская СОШ ','Гридчина Таисия Алексеевна','(8-231)5-92-04','309218, Корочанский р-он, с.Бехтеевка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,6,'МОУ Большехаланская СОШ ','Водяха Ольга Ивановна','(8-231)4-91-25','309213, Корочанский р-он, с.Большая Халань','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,7,'МОУ Жигайловская СОШ ','Спивак Лидия Николаевна','(8-231)3-47-85','309234, Корочанский р-он, с.Жигайловка','','gig.shkola@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,8,'МОУ Кощеевская СОШ ','Столбовская Нина Николаевна','(8-231)4-72-38','309223, Корочанский р-он, с.Кощеево','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,9,'МОУ Коротковская СОШ ','Чекрыкина Татьяна Никифоровна','(8-231)4-61-44','309209, Корочанский р-он, с.Короткое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,10,'МОУ Ломовская СОШ ','Чуева Валентина Дмитриевна','(8-231)4-41-22','309204, Корочанский р-он, с.Ломово','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,11,'МОУ Мелиховская СОШ ','Балабанова Людмила Евдокимовна','(8-231)3-07-68','309201, Корочанский р-он, с.Мелихово','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,12,'МОУ Новослободская СОШ ','Цуркин Виталий Георгиевич','(8-231)4-32-10','309222, Корочанский р-он, с.Новая Слободка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,13,'МОУ Погореловская СОШ ','Черкасов Александр Егорович','(8-231)5-65-75','309220, Корочанский р-он, с.Погореловка','','pshol@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,14,'МОУ Поповская СОШ ','Агарков Виктор Андреевич','(8-231)5-71-93','309225, Корочанский р-он, с.Поповка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,15,'МОУ Плотавская СОШ ','Цуркин Анатолий Павлович','(8-231)3-76-36','309226, Корочанский р-он, с.Плотавец','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,16,'МОУ Проходенская СОШ ','Кийков Александр Викторович','(8-231)5-22-69','309219, Корочанский р-он, с.Проходное','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,17,'МОУ Соколовская СОШ ','Заикин Александр Анатольевич','(8-231)3-15-41','309237, Корочанский р-он, с.Соколовка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,18,'МОУ Шеинская СОШ ','Нестерова Елена Николаевна','(8-231)3-95-36','309202, Корочанский р-он, с.Шеино','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (10,19,'МОУ Яблоновская СОШ ','Романенко Леонид Михайлович','(8-231)3-33-38','309216, Корочанский р-он, с.Яблоново ','','yablonovsh@beltts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,1,'МОУ Ивнянская СОШ  №1','Кременчутская Наталья Николаевна','(8-248)5-16-96,(8-248)5-12-90','309110, п.Ивня, ул.Советская, 42','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,2,'МОУ Ивнянская СОШ №2','Кременев Владимир Михайлович','(8-248)5-16-96,(8-248)5-55-87','309110, п.Ивня, пер.Гагаринский,28','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,3,'МОУ Богатеская СОШ ','Морозов Юрий Евгеньевич','(8-248)47-3-24','309114, Ивнянский р-он, с.Богатое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,4,'МОУ Верхопенская СОШ им.М.Р.Абросимова','Билецкая Татьяна Дмитриевна','(8-248)4-64-69,(8-248)4-64-59','309135, Ивнянский р-он, с.Верхопенье','','scool@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,5,'МОУ Владимировская СОШ ','Родионова Лариса Анатольевна','(8-248)41-2-74','309130, Ивнянский р-он, с.Владимировка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,6,'МОУ Вознесеновская СОШ ','Семенов Юрий Васильевич','(8-248)41-2-69','309130, Ивнянский р-он, с.Вознесеновка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,7,'МОУ Драгунская СОШ ','Алпеев Иван Дмитриевич','(8-248)49-2-18','309123, Ивнянский р-он, с. Драгунка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,8,'МОУ Кочетовская СОШ ','Дворников Алексей Алексеевич','(8-248)44-1-44','309133, Ивнянский р-он, с.Кочетовка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,9,'МОУ Курасовская СОШ ','Еремина Нина Михайловна','(8-248)41-2-32,(8-248)41-2-34','309116, Ивнянский р-он, с.Курасовка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,10,'МОУ Новенская СОШ ','Мироненко Евгений Михайлович','(8-248)43-3-75','309115, Ивнянский р-он, с.Новенькое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,11,'МОУ Песчанская СОШ ','Махова Галина Евгеньевна','(8-248)40-1-19','309121, Ивнянский р-он, с.Песчаное','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,12,'МОУ Покровская СОШ ','Попова Елена Владимировна','(8-248)47-4-24','309118, Ивнянский р-он, с.Покровка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,13,'МОУ Сафоновская СОШ ','Макаров Григорий Иванович','(8-248)5-56-45','309123, Ивнянский р-он, с.Сафоновка','','makar 55 @ rambler.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,14,'МОУ Сухосолотинская СОШ ','Лупандин АлександрАлексеевич','(8-248)47-2-31','309134, Ивнянский р-он, с.Сухосолотино','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,15,'МОУ Сырцевская СОШ ','Брыткова Людмила Алексеевна','(8-248)45-6-46','309136,Ивнянский р-он, с.Сырцево','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (9,16,'МОУ Хомутчанская СОШ ','Селихова Галина Николаевна','(8-248)48-1-22','309122, Ивнянский р-он, сХомутцы','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,1,'МОУ СОШ №1 с углубленным изучением отдельных предметов ','Колесникова  Галина Ивановна','(8-241)4-60-82,(8-241)4-65-85,(8-241)4-64-30','309510 Белгородская обл., г.Губкин, ул. Победы,24','','shcoo1001@ mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,2,'МОУ СОШ №2  с углублённым изучением отдельных предметов','Ерёмин Николай Михайлович','(8-241)5-57-33,(8-241)4-67-63;ф: (8-241)4-67-61','309186, г. Губкин, ул. Чайковского,12','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,3,'МОУ СОШ №3 ','Скворцова Зинаида Сергеевна','(8-241)3-54-41;(8-241)3-35-76','309190 г. Губкин ,ул. Лазарева 13','','SGOL03_BELG@tts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,4,'МОУ Лицей №5','Сергеев Николай Иванович','(8-241)3-42-27,(8-241)3-52-68','309181 г.Губкин, ул. Советская, д.29','','avega@kma.ru.');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,5,'МОУ Гимназия № 6','Машкин Виктор Федорович','(8-241)3-49-24;(8-241)3-42-28','309181 г.Губкин, ул. Советская, 27','','school06@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,6,'МОУ СОШ №7','Пахомов Геннадий Семенович','(8-241)6-33-83','г. Губкин, ул. П.Морозова, 2','','hooman@yandex.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,7,'МОУ СОШ №8','Модлина Светлана Александровна','(8-241)6-59-05','309185, г. Губкин, ул. Ударников, 12','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,8,'МОУ СОШ № 10 ','Соломахина Татьяна Петровна','(8-241)6-52-67,(8-241)6-52-76','309187, г.Губкин, ул.Белгородская, д.349','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,9,'МОУ СОШ № 11','Дутов Анатолий Иванович','(8-241)4-75-85;(8-241)4-09-43;(8-241)4-06-83','309188  г.Губкин, ул. К.Маркса, 21а','','School11@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,10,'МОУ СОШ № 12 с углублённым изучением отдельных предметов','Псарева Лариса Васильевна','(8-241)32-6-94,(8-241)32-4-87,','309181, г.Губкин, ул.Лазарева, 15','','sch_12@mail.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,11,'МОУ СОШ №13 с углубленным изучением отдельных предметов','Крылова Людмила Павловна','(8-241)33-0-83','309190, г. Губкин, ул. Раевского, 15а','','School13@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,12,'МОУ СОШ № 15','Шумская Ольга Викторовна','(8-241)6-38-95','510184, г. Губкин, ул. П.Морозова, 2','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,13,'МОУ СОШ № 16','Скаржинский Ярослав Христианович','(8-241)4-07-83(8-241)4-07-85','309186, , г.Губкин, ул. Воинов-Интернационалистов,1','','SH16@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,14,'МОУ СОШ  № 17','Бантюкова Галина Григорьевна','(8-241)4-17-05','309183 г. Губкин, микрорайон <Журавлики>','','Scholl17@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,15,'МОУ Аверинская СОШ ','Богданова Валентина Дмитриевна','(8-0225)6-07-74','309141, Губкинский район, с.Аверино','','averino@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,16,'МОУ Архангельская СОШ ','Степанова Ольга Дмитриевна','(8-0225)64 7 66','309153 Губкинский районс. Архангельское','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,17,'МОУ Боброводворская СОШ ','Филиппова Ирина Николаевна','(8-0225)6-60-39','309170, Губкинский район с. Бобровы Дворы ','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,18,'МОУ Богословская СОШ ','Глухенко Иван Николаевич','(8-0225)6-93-34','309173 Губкинский район, с. Богословка  ','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,19,'МОУ Вислодубравская СОШ ','Гекова Наталья Ивановна','(8-0225)6-95-32','309154, Губкинский район, с.Вислая Дубрава','','utro3002@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,20,'МОУ Ивановская СОШ ','Семиненко Елена Александровна','(8-0225)69-4-42','309151 Губкинский район, с.Ивановка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,21,'МОУ Истобнянская СОШ ','Бежина Инна Леонидовна','(8-0225)6-41-36.','309160, Губкинский район, с. Истобное','','ISTOBNOE@BELGTTS.RU');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,22,'МОУ Коньшинская СОШ ','Леонова  Валентина  Викторовна','(8-0225)64-8-47','309174, Губкинский  район, с. Коньшино  ','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,23,'МОУ Никаноровская СОШ ','Извекова Галина Николаевна','(8-0225)6-90-23','309162. Губкинский район, с.Никаноровка','','nikanorovka@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,24,'МОУ Сапрыкинская СОШ ','Лубышева Людмила Николаевна','(8-0225)6-43-34,(8-0225)6-43-19','309172, Губкинский район, с. Сапрыкино','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,25,'МОУ Сергиевская СОШ ','Шестакова Вера Васильевна','(8-0225)6-01-86','309527, Губкинский район, с. Сергиевка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,26,'МОУ Скороднянская СОШ ','Буянова Нина Алексеевна','(8-0225)67-2-01','309163, , с. Скородное','','skorodnoe1@km.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,27,'МОУ Толстянская СОШ ','Бочарова Тамара Ивановна','(8-0225)6-87-24','309164, Губкинский район, с. Толстое','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,28,'МОУ Троицкая СОШ ','Суровцева Наталья Ивановна','(8-0225)78-4-41','309145 Губкинский район;п.Троицкий','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,29,'МОУ Уколовская  СОШ ','Уколова Надежда Петровна','(8-0225)6-03-42','309165, Губкинский район с.Уколово ','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,30,'МОУ Чуевская СОШ ','Чуева Валентина Ивановна','(8-0225)6-44-22','309166 Губкинский район с. Чуево   ','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (8,31,'МОУ Юрьевская СОШ ','Горенко Людмила Владимировна','(8-0225)6-85-88,(8-0225)6-85-23','309167 Губкинский район, с. Юрьевка','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,1,'МОУ Алексеевская СОШ ','Удрит Светлана Николаевна','(8-244)64-2-61','309074, с.Алексеевка, ул.Центральная,16а','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,2,'МОУ Бутовская СОШ','Савчук Людмила Семеновна','(8-244)4-32-32','309093, с.Бутово, ул.Магистральная 44','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,3,'МОУ Быковская СОШ','Павленко Анжела Михайловна','(8-244)6-71-17','309091, с.Быковка, ул.Центральная,64','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,4,'МОУ Гостищевская СОШ','Борисов Юрий Иванович','(8-244)6-32-67','309050, с.Гостищево, ул.Учительская 9а  ','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,5,'МОУ Дмитриевская СОШ','Меденцев Роман Викторович','(8-244)68-2-10','309063, с.Дмитриевка, ул. Молодежная 14','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,6,'МОУ Завидовская СОШ','Соколова Валентина Демьяновна','(8-244)6-87-10','309075, с.Завидовка, ул.Школьная,1','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,7,'МОУ Казацкая СОШ','Сальтевская  Надежда Викторовна','(8-244)4-16-38','309095, с.Казацкое, ул.Центральная,4','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,8,'МОУ Кривцовская СОШ','Романькова Надежда Ивановна','(8-244)6-85-97','309052, с.Кривцово, ул.Молодежная,22','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,9,'МОУ Кустовская СОШ','Саенко Сергей Владимирович','(8-244)4-24-41','309081, сКустовое, ул.Победы, 5-а','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,10,'МОУ Стрелецкая СОШ','Кальницкая Нина Васильевна','(8-244)4-34-85','309087, с.Стрелецкое, пер.Школьная,2','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,11,'МОУ СОШ№1, г.Строитель','Капустин Владимир Владимирович','(8-244)5-05-08','309071, г.Строитель, ул.Ленина,9','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,12,'МОУ СОШ№2, г.Строитель','Корниенко Борис Михайлович','(8-244)5-34-57','309070, г.Строитель, ул.Ленина, 24','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,13,'МОУ СОШ №3, г.Строитель','Курилова Нина Павловна','(8-244)5-30-91','309071, г.Строитель, ул.Победы, 7','','stroitel-1@belgtts.ru');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,14,'МОУ Терновская СОШ','Негодина Ирина Николаевна','(8-244)66-1-22','309060 , с.Терновка, ул.Центральная,11','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,15,'МОУ Томаровская СОШ №1','Глушков Владимир Васильевич','(8-244)4-53-99','309085, п.Томаровка, ул.Ленина ,11','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,16,'МОУ Томаровская СОШ №2','Куценко Лидия Владимировна','(8-244)4-41-93','309085, п.Томаровка,ул.32-Гв.Корпуса, 15-а','','');
INSERT INTO `prefix_monit_school` (`rayonid`, `number`, `name`, `fio`, `phones`, `address`, `www`, `email`) VALUES (20,17,'МОУ Яковлевская СОШ','Шулякова Валентина Ивановна','(8-244)62-4-48','309076,  п.Яковлево, ул.Угловского, 16','','');

# Таблицы форм БКП

CREATE TABLE  `prefix_monit_status` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  `color` varchar(45) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO prefix_monit_status VALUES (1,'Новый','FF0000');
INSERT INTO prefix_monit_status VALUES (2,'В работе','FFB900');
INSERT INTO prefix_monit_status VALUES (3,'Доработать','BE8641');
INSERT INTO prefix_monit_status VALUES (4,'На согласовании','yellow');
INSERT INTO prefix_monit_status VALUES (5,'Архив','1BE4D8');
INSERT INTO prefix_monit_status VALUES (6,'Принят','00FF59');
INSERT INTO prefix_monit_status VALUES (7,'Принят с недоработками','92B34D');

CREATE TABLE  `prefix_monit_form` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `period` varchar(10) NOT NULL,
  `levelmonit` varchar(10) NOT NULL,
  `reported` TINYINT(1) NOT NULL default 0,
  `fullname` varchar(350) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO prefix_monit_form (name, period, levelmonit)  VALUES ('Региональные формы', 'month', 'region');
INSERT INTO prefix_monit_form (name, period, levelmonit)  VALUES ('Муниципальные формы', 'month', 'rayon');
INSERT INTO prefix_monit_form (name, period, levelmonit)  VALUES ('Формы ООУ', 'month', 'school');

INSERT INTO prefix_monit_form (name, period, levelmonit, reported, fullname)  VALUES ('Обязательства', 'month', 'region', 1, 'Мониторинг достижения взятых обязательств по внедрению комплексного проекта модернизации образования субъектом Российской Федерации Белгородская область в ');
INSERT INTO prefix_monit_form (name, period, levelmonit, reported, fullname)  VALUES ('Действия', 'month', 'region', 1, 'Мониторинг выполнения контрольных мероприятий по внедрению комплексного проекта модернизации образования субъектом Российской Федерации Белгородская область на ');
INSERT INTO prefix_monit_form (name, period, levelmonit, reported, fullname)  VALUES ('Текущие показатели', 'month', 'region', 1, 'Промежуточные данные для определения достижения взятых обязательств по внедрению комплексного проекта модернизации образования субъектом Российской Федерации Белгородская область в ');
INSERT INTO prefix_monit_form (name, period, levelmonit, reported, fullname)  VALUES ('Нормативные документы', 'month', 'region', 1, 'Нормативные документы субъекта Российской Федерации Белгородская область');
INSERT INTO prefix_monit_form (name, period, levelmonit, reported, fullname)  VALUES ('Текущие показатели и нормативные документы по ООУ', 'month', 'school', 1, 'Текущие данные для определения достижения взятых обязательств по внедрению комплексного проекта модернизации образования ');

CREATE TABLE  `prefix_monit_razdel` (
  `id` int(10) NOT NULL auto_increment,
  `formid` int(10) NOT NULL,
  `name` varchar(255) default NULL,
  `shortname` VARCHAR(20) default NULL,
  `help` varchar(255) default NULL,
  `reported` TINYINT(1) NOT NULL default 0,
   PRIMARY KEY  (`id`),
  KEY `formid` (`formid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO prefix_monit_razdel (formid, name, shortname)  VALUES (1, 'РКП-пр.р (РО)', 'rkp_prr_ro');
INSERT INTO prefix_monit_razdel (formid, name, shortname)  VALUES (1, 'РКП-пр.р (РЭ)', 'rkp_prr_eks');
INSERT INTO prefix_monit_razdel (formid, name, shortname)  VALUES (1, 'РКП-егэ (РО)', 'rkp_ege');
INSERT INTO prefix_monit_razdel (formid, name, shortname)  VALUES (1, 'РКП-д (РО)', 'rkp_d');
INSERT INTO prefix_monit_razdel (formid, name, shortname)  VALUES (2, 'РКП-пр.м (МО)', 'rkp_prm_mo');
INSERT INTO prefix_monit_razdel (formid, name, shortname)  VALUES (2, 'РКП-пр.м (РО)', 'rkp_prm_eks');
INSERT INTO prefix_monit_razdel (formid, name, shortname)  VALUES (3, 'РКП-у (ООУ)', 'rkp_u');
INSERT INTO prefix_monit_razdel (formid, name, shortname)  VALUES (3, 'РКП-д/у (ООУ)', 'rkp_du');
INSERT INTO prefix_monit_razdel (formid, name, shortname)  VALUES (3, 'БКП-пред (ООУ)', 'bkp_pred');
INSERT INTO prefix_monit_razdel (formid, name, shortname)  VALUES (3, 'БКП-дл (ООУ)', 'bkp_dolj');
INSERT INTO prefix_monit_razdel (formid, name, shortname)  VALUES (3, 'БКП-ф (ООУ)', 'bkp_f');

INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (4, 'Введение новой системы оплаты труда учителей (НСОТ)', 1);
INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (4, 'Переход на нормативное подушевое финансирование общеобразовательных учреждений (НПФ)', 1);
INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (4, 'Развитие региональной системы оценки качества образования (РСОКО)', 1);
INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (4, 'Развитие сети общеобразовательных учреждений региона', 1);
INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (4, 'Расширение общественного участия в управлении образованием', 1);
INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (5, '', 1);
INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (6, 'Информация о региональной системе образования', 1);
INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (6, 'Введение новой системы оплаты труда учителей (НСОТ)', 1);
INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (6, 'Переход на нормативное подушевое финансирование общеобразовательных учреждений (НПФ)', 1);
INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (6, 'Развитие региональной системы оценки качества образования (РСОКО)', 1);
INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (6, 'Развитие сети общеобразовательных учреждений региона: обеспечение условий для получения качественного общего образования', 1);
INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (6, 'Расширение общественного участия в управлении образованием', 1);
INSERT INTO prefix_monit_razdel (formid, name, reported)  VALUES (6, 'Организационное обеспечение реализации РКП', 1);

INSERT INTO prefix_monit_razdel (formid, name, shortname, reported)  VALUES (8, 'Общие сведения об учреждении', 'rkp_u', 1);
INSERT INTO prefix_monit_razdel (formid, name, shortname, reported)  VALUES (8, 'Введение новой системы оплаты труда учителей (НСОТ)', 'rkp_u', 1);
INSERT INTO prefix_monit_razdel (formid, name, shortname, reported)  VALUES (8, 'Переход на нормативное подушевое финансирование общеобразовательных учреждений (НПФ)', 'rkp_u', 1);
INSERT INTO prefix_monit_razdel (formid, name, shortname, reported)  VALUES (8, 'Развитие сети общеобразовательных учреждений региона: обеспечение условий для получения качественного общего образования', 'rkp_u', 1);
INSERT INTO prefix_monit_razdel (formid, name, shortname, reported)  VALUES (8, 'Расширение общественного участия в управлении образованием', 'rkp_u', 1);
INSERT INTO prefix_monit_razdel (formid, name, shortname, reported)  VALUES (8, 'Организационное обеспечение реализации РКП', 'rkp_du', 1);


CREATE TABLE  `prefix_monit_razdel_field` (
  `id` int(10) NOT NULL auto_increment,
  `razdelid` int(10) NOT NULL,
  `name` text NOT NULL default '',
  `help` varchar(255) default NULL,
  `name_field` varchar(32) default NULL,
  `edizm` varchar(10) default NULL,
  `calcfunc` varchar(255) default NULL,
  `timecalculated` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `razdelid` (`razdelid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Введение новой системы оплаты труда учителей (НСОТ)','f1_','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Номинальное значение средней зарплаты учителей в среднем по региону по состоянию на 2006 г','f1_2_0','rub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Переход на нормативное подушевое финансирование общеобразовательных учреждений (НПФ)','f2_','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Величина норматива на городского школьника','f2_4_1r','rub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Ссылка на размещенный в сети Интернет нормативный акт, определяющий данный показатель норматива на городского школьника','f2_4_1g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Величина норматива (или среднее значение фактических расходов) на городского школьника до начала проекта','f2_4_1_0','rub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Ссылка на размещенный в сети Интернет нормативный акт, определяющий показатель норматива на городского школьника (до начала проекта)','f2_4_1_0g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Величина норматива на сельского школьника','f2_4_2r','rub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Ссылка на размещенный в сети Интернет нормативный акт, определяющий данный показатель норматива на сельского школьника','f2_4_2g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Величина норматива (или среднее значение фактических расходов) на сельского школьника до начала проекта','f2_4_2_0','rub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Ссылка на размещенный в сети Интернет нормативный акт, определяющий данный показатель норматива на сельского школьника (до начала проекта)','f2_4_2_0g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Дата принятия нормативного правового акта субъекта РФ, устанавливающего нормативы бюджетного финансирования общеобразовательных учреждений на реализацию госстандарта (зарплату и учебные расходы)','f2_5_1r','data');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Ссылка на размещенный в сети Интернет текст нормативного правового акта субъекта РФ, устанавливающего нормативы бюджетного финансирования общеобразовательных учреждений на реализацию госстандарта (зарплату и учебные расходы)','f2_5_1g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Развитие региональной системы оценки качества образования (РСОКО)','f3_','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Ссылка на Интернет-страницу, содержащую отчет о проведении одного обследования учебных достижений учащихся любой параллели на региональном уровне дополнительно к ЕГЭ','f3_6_1g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Ссылка на нормативный акт о создании структуры (организации), на базе которой обрабатываются и обобщаются результаты оценки качества образования (ЕГЭ, новая форма государственной (итоговой) аттестации выпускников IX классов общеобразовательных учреждений, аккредитация общеобразовательных учреждений, мониторинговые исследования и др.) с указанием функционала','f3_6_2g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Ссылка на Интернет-страницу, содержащую текст Положения о РСОКО','f3_7_1g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Ссылка на Интернет-страницу, содержащую тексты документов, определяющих регламент функционирования РСОКО','f3_7_2g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Ссылка на Интернет-страницу, содержающую тексты нормативных правовых документов, предусматривающих учет при оценке качества образования внеучебных достижений учащихся','f3_8g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Развитие сети общеобразовательных учреждений региона: обеспечение условий для получения качественного общего образования независимо от места жительства','f4_','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (1,'Количество работников общеобразовательных учреждений по состоянию на начало проекта (на 5 сентября 2006 г.)','f4_5_0','man');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Организационное обеспечение реализации РКП','f6_','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Наличие регионального сетевого графика реализации РКП','f6_1r','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Наличие инструктивно-методических материалов  для управленческих команд на муниципальном уровне и уровне общеобразовательного учреждения по ','f6_1_1','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Введению НСОТ','f6_2_1','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Переходу на НПФ','f6_2_2','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Развитию РСОКО','f6_2_3','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Развитию сети','f6_2_4','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Расширению общественного участия в управлении образованием','f6_2_5','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Наличие программ подготовки по:','f6_3_','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Введению НСОТ','f6_3_1','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Переходу на НПФ','f6_3_2','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Развитию РСОКО','f6_3_3','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Развитию сети','f6_3_4','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (2,'Расширению общественного участия в управлении образованием','f6_3_5','bool');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (3,'Количество учебных предметов, по которым выпускникам XI (XII) классов общеобразовательных учреждений (текущего года) предоставляется возможность прохождения государственной (итоговой) аттестации в форме ЕГЭ','f3_2','item');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (3,'Количество выпускников XI (XII) классов общеобразовательных учреждений (текущего года), проходящих итоговую аттестацию по русскому языку в форме ЕГЭ','f3_3_1r','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (3,'Количество выпускников XI (XII) классов общеобразовательных учреждений (текущего года), проходящих итоговую аттестацию по математике в форме ЕГЭ','f3_3_2r','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (3,'Количество выпускников XI (XII)  классов, проходящих итоговую аттестацию по трем и более предметам в форме ЕГЭ','f3_4r','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (3,'Количество выпускников IX классов общеобразовательных учреждений (текущего года), проходящих государственную (итоговую) аттестацию по русскому языку в новой форме','f3_5_1r','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (3,'Количество выпускников IX классов общеобразовательных учреждений (текущего года), проходящих государственную (итоговую) аттестацию по математике в новой форме','f3_5_2r','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (3,'Количество пунктов проведения итоговой аттестации выпускников старшей ступени общего образования в форме ЕГЭ','f5_5_1','item');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (3,'Количество пунктов проведения итоговой аттестации выпускников старшей ступени общего образования в форме ЕГЭ, в которых присутствовали общественные наблюдатели','f5_5_2','item');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Принятие нормативного акта органа власти субъекта РФ, утверждающего содержание и запускающего реализацию РКП','fd1_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Предусмотрение в региональном бюджете средств на реализацию РКП на 2007 год','fd2_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Создание региональной рабочей группы (РРГ) по реализации РКП','fd3_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Создание регионального общественного совета (РОС) при высшем должностном лице субъекта РФ по реализации РКП ','fd4_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Заключение соглашений с муниципальными образованиями, участвующими в реализации РКП в 2007 году','fd5_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Публичная презентация и организация общественного обсуждения запуска РКП','fd6_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Создание регионального сайта РКП и его наполнение материалами, включая все принятые нормативные акты по реализации РКП, информацию об обязательствах региона по основным направлениям РКП, состав и контакты РРГ и РОС','fd7_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Объявление конкурсов на поставку товаров, выполнение работ, оказание услуг','fd8_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'1 транш','fd8_0','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'2 транш','fd8_1','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'3 транш','fd8_2','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Заключение контрактов на поставку товаров, выполнение работ, оказание услуг:','fd9_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'1 транш','fd9_0','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'2 транш','fd9_1','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'3 транш','fd9_2','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Завершение контрактов на поставку товаров, выполнение работ, оказание услуг','fd10_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Принятие нормативного акта субъекта РФ о введении НСОТ','fd11_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Разработка инструктивно-методических материалов по введению НСОТ','fd12_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Обучение управленческих команд (включая руководителя, финансиста, представителя управляющего совета) образовательных учреждений, в которых вводится НСОТ с 2007 года, по вопросам финансово-хозяйственной самостоятельности ОУ и введения НСОТ в условиях НПФ','fd13_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Мониторинг принятия в ОУ, вводящих НСОТ с 2007 г. локальных нормативных актов: о введении и реализации НСОТ; о деятельности органа общественного управления и порядке его участия в распределении стимулирующей части ФОТ – и их качества','fd14_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Мониторинг в муниципальных образованиях: расходования средств, переданных муниципальным образованиям в рамках внедрения РКП; уровня социальной напряженности (в пилотных территориях по введению НСОТ)','fd15_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Подготовка и размещение на региональном сайте РКП аналитических материалов о запуске РКП, включая анализ мероприятий по предупреждению и снятию социальной напряженности','fd16_','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (4,'Заседание РОС: рассмотрение хода реализации РКП в 2007 г.','fd17_','expl');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (5,'Данное муниципальное образование является пилотным в рамках проекта реализации РКПМО','f0_0_0m','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (5,'Расширение общественного участия в управлении образованием','f5_','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (5,'Ссылка на Интернет-страницу, содержащую муниципальный нормативный акт о создании органа государственно-общественного управления образованием, ориентированного на его развитие, в том числе обладающего полномочиями по распределению фонда стимулирования руководителей общеобразовательных учреждений','f5_2_0m','link');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (6,'Расширение общественного участия в управлении образованием','f5_','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (6,'Есть ли в муниципальном образовании орган государственно-общественного управления образованием, ориентированный на его развитие, в том числе обладающий полномочиями по распределению фонда стимулирования руководителей общеобразовательных учреждений','f5_2m','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (6,'Организационное обеспечение реализации РКП','f6_','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (6,'Прошла ли управленческая команда муниципального образования подготовку по:','f6_0','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (6,'Введению НСОТ','f6_3_7_1','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (6,'Переходу на НПФ','f6_3_7_2','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (6,'Развитию РСОКО','f6_3_7_3','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (6,'Развитию сети','f6_3_7_4','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (6,'Расширению общественного участия в управлении образованием','f6_3_7_5','bool');


INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Информация о системе образования на всех уровнях','f0','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Количество учащихся (текущее) ','f0_1u','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Количество классов (текущее) ','f0_2u','item');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Количество работников в ОУ (текущее) ','f0_3u','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Количество учителей (текущее) ','f0_4u','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Годовой бюджет учреждения ','f0_5u','item');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'ФОТ учреждения ','f0_6u','trub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'ФОТ учителей учреждения ','f0_7u','trub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Является учреждение городским или сельским ','f0_8u','gets');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Текущее количество учащихся IX классов ','f0_9u','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Текущее количество учащихся X классов (или X и XI классов для вечерних учреждений) ','f0_10u','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Текущее количество учащихся XI классов (или XII классов для вечерних школ) ','f0_11u','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Введение новой системы оплаты труда учителей (НСОТ)','f1','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Перешло ли учреждение на НСОТ ','f1_5u','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Дата принятия Нормативного акта учреждения о НСОТ ','f1_5g1','data');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Реквизиты Нормативного акта учреждения о НСОТ ','f1_5g2','text');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Ссылка на страницу в сети Интернет, содержащую текст Нормативного акта учреждения о НСОТ ','f1_5g3','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Переход на нормативное подушевое финансирование общеобразовательных учреждений (НПФ)','f2','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Получает ли учреждение финансирование по нормативу?','f2_1u','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Имеет ли учреждение финансовую самостоятельность?','f2_3u','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Развитие сети общеобразовательных учреждений региона: обеспечение условий для получения качественного общего образования независимо от места жительства','f4','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Наличие в учреждении 1 ступени общего образования ','f4_0_1','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Наличие в учреждении 2 ступени общего образования ','f4_0_2','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Наличие в учреждении 3 ступени общего образования ','f4_0_3','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Полная обеспеченность предметов федерального компонента базисного учебного плана учителями в соответствии со специальностью (квалификацией), что подтверждается документом о профессиональном образовании или профессиональной переподготовке ','f4_1_8','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Обеспеченность программ профильного обучения и предпрофильной подготовки учителями не ниже 2 квалификационной категории ','f4_1_9','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Расширение общественного участия в управлении образованием','f5','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Есть ли в учреждении согласно зарегистрированному уставу орган государственно-общественного управления (совет), обладающий комплексом управленческих полномочий, в том числе, по распределению средств стимулирующей части фонда оплаты труда ','f5_1u','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Дата принятия нормативного документа (локального акта ОУ), подтверждающего наличие органа государственно-общественного управления (совета) ','f5_1g1','data');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Реквизиты нормативного документа (локального акта ОУ), подтверждающего наличие органа государственно-общественного управления (совета) ','f5_1g2','text');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Ссылка на размещенный в сети Интернет нормативный документ (локальный акт ОУ), подтверждающий наличие органа государственно-общественного управления (совета) ','f5_1g3','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Имеет ли учреждение опубликованный публичный отчет об образовательной и финансово-хозяйственной деятельности за прошедший учебный год? ','f5_3_0u','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Ссылка на размещенный в сети Интернет публичный отчет об образовательной и финансово-хозяйственной деятельности ','f5_3g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Имеет ли образовательное учреждение собственный сайт в сети Интернет ','f5_4_1u','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (7,'Ссылка на сайт образовательного учреждения ','f5_4g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (8,'Проведение общественного обсуждения плана участия общеобразовательного учреждения в РКП','fd_u_1','expl');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (8,'Принятие локального нормативного акта общеобразовательного учреждения о введении (апробации) НСОТ','fd_u_2','expl');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'Количество учителей по предметам:','f0','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Биология»','ku_biologia','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«География»','ku_geografia','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Естествознание»','ku_estestvozn','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Иностранный язык»','ku_in_jaz','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Информатика и ИКТ»','ku_ikt','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Искусство (Музыка и ИЗО)»','ku_iskusstvo','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«История»','ku_istoria','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Литература»','ku_literatura','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Литературное чтение»','ku_lit_chtenie','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Математика»','ku_matem','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Мировая художественная культура»','ku_mhk','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Обществоведение»','ku_obved','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Обществознание»','ku_obzn','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Обществознание (включая экономику и право)»','ku_obzn_pek','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Окружающий мир (человек, природа, общество)»','ku_okr_mir','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Основы агрономии»','ku_osn_agr','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Основы безопасности жизнедеятельности»','ku_obg','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Основы животноводства»','ku_osn_giv','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Право»','ku_pravo','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Православная культура»','ku_pkult','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Природоведение»','ku_prirodoved','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Профильные учебные предметы искусства»','ku_prof_isk','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Русский язык»','ku_rus_jaz','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Сельскохозяйственная техника»','ku_schoz_techn','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Технология»','ku_techn','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Технология ( включая электротехнику и радиоэлектронику)»','ku_techn_et','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Технология (Труд) Информатика и ИКТ»','ku_techn_ikt','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Физика»','ku_phisika','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Физическая  культура»','ku_phis_kult','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Химия»','ku_chim','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Экология Белгородской области»','ku_ek_bo','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (9,'«Экономика»','ku_ekonom','man');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Список должностей работников учреждения','f0','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Руководители','f1','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество директоров (генеральный директор, начальник, заведующий, управляющий) образовательного учреждения','r_dir','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество заместителей руководителя образовательного учреждения','r_zam_dir','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество руководителей структурного подразделения учреждения образования','r_ruk_str_podrazd','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество главных бухгалтеров','r_gl_buch','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество главных инженеров','r_gl_ing','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество заведующих филиалом библиотеки (централизованной библиотечной системы)','r_zav_fil_bibl','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество заведующих архивом','r_zav_arch','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество заведующих лабораторией','r_zav_lab','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество заведующих хозяйством','r_zav_choz','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество заведующих складом','r_zav_sklad','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество начальников (заведующих) мастерской','r_nach_master','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество заведующих столовой','r_zav_stol','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество управляющих отделением (фермой, сельскохозяйственным участком)','r_upr_o','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество заведующих производством (шеф-повар)','r_zav_pr','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество преподавателей-стажеров','r_prep_stager','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Специалисты ','f2','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество учителей','s_uch','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество преподавателей','s_prep','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество учителей-дефектологов, учителей-логопедов, логопедов','s_uch_defekt','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество преподавателей-организаторов (основ безопасности жизнедеятельности, допризывной подготовки)','s_prep_org','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество руководителей физического воспитания','s_ruk_fis_vosp','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество мастеров производственного обучения','s_master_pr_obuch','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество методистов, инструкторов-методистов (включая старшего)','s_metodist','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество концертмейстеров','s_koncert','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество музыкальных руководителей','s_mus_ruk','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество аккомпаниаторов','s_akk','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество воспитателей (включая старшего)','s_vosp','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество классных воспитателей','s_kl_vosp','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество социальных педагогов','s_soc_ped','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество педагогов-психологов','s_ped_psich','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество педагогов-организаторов','s_ped_org','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество педагогов дополнительного образования','s_ped_dop_obr','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество тренеров-преподавателей образовательного учреждения (включая старшего)','s_trener','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество старших вожатых','s_st_vog','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество инструкторов по труду','s_instr_trud','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество инструкторов по физической культуре','s_instr_fis_kult','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество диспетчеров (включая старшего)','s_dispetch','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество инспекторов (включая старшего)','s_inspect','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество лаборантов (включая старшего)','s_labor','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество техников','s_technik','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество бухгалтеров','s_buch','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество бухгалтеров-ревизоров','s_buch_rev','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество инженеров','s_ingener','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество экономистов','s_ekonomist','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество товароведов','s_tov_ved','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество художников','s_chud','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество юрисконсультов','s_jur','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество электроников','s_elektronik','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество программистов','s_programm','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество документоведов','s_doc_ved','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество сурдопереводчиков','s_surdo','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество специалистов по кадрам','s_spec_kadr','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество администраторов (включая старшего)','s_admin','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Учебно-вспомогательный персонал','f3','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество библиотекарей','uvp_bibl','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество дежурных по общежитию','uvp_deg_obch','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество дежурных по режиму (включая старшего)','uvp_deg_regim','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество младших воспитателей','uvp_ml_vosp','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество механиков','uvp_mech','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество помощников воспитателя','uvp_pom_vosp','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Технические исполнители и обслуживающий персонал','f4','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество экспедиторов','tiop_ekspeditor','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество делопроизводителей','tiop_delopr','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество секретарей','tiop_sekr','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество секретарь-машинистка','tiop_sekr_mash','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество архивариусов','tiop_archiv','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество кассиров (включая старшего)','tiop_kassir','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество машинисток','tiop_mash','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество экспедиторов по перевозке грузов','tiop_ekspeditor_grus','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество возчиков','tiop_voz','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество водителей автомобиля','tiop_vod4','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество гардеробщиков','tiop_garder','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество горничных','tiop_gornich','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество грузчиков','tiop_grus1','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество дворников','tiop_dvor','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество истопников','tiop_istop','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество кастелянш','tiop_kast1','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество кладовщиков','tiop_klad1','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество конюхов','tiop_kon1','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество лаборантов химического анализа','tiop_labor2','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество механиков по техническим видам спорта','tiop_mech_tech_sport','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество нянь','tiop_nn','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество операторов электронно-вычислительных и вычислительных машин','tiop_op_evvm','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество подсобных рабочих','tiop_pr1','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество плотников','tiop_pl3','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество рабочих по комплексному обслуживанию и ремонту зданий','tiop_korz','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество столяров строительных','tiop_st3','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество слесарей-сантехников','tiop_sl2','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество рабочих по уходу за животными','tiop_rgiv','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество ремонтировщиков плоскостных спортивных сооружений','tiop_rsport','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество садовников','tiop_sad1','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество сторожей (вахтеров)','tiop_st_vacht','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество трактористов','tiop_trakt','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество уборщиков производственных и служебных помещений','tiop_ub_pom','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (10,'Количество уборщиков территорий','tiop_ub_terr','man');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'Годовой бюджет учреждения, в т.ч.','f1','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'- за счет субвенций областного бюджета','f1f','trub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'- за счет субвенций местного бюджета','f2f','trub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'- из внебюджетных источников','f3f','trub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'Годовой фонд оплаты труда общеобразовательного учреждения, в т.ч.','f2', 'null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'Фонд оплаты труда директора','f4f','trub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'Фонд оплаты труда заместителей директоров','f5f','trub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'Фонд оплаты труда педагогических работников','f6f','trub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'Фонд оплаты труда учебно-вспомогательного персонала','f7f','trub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'Фонд оплаты труда обслуживающего персонала','f8f','trub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'Фонд оплаты труда учителей, в т.ч.','f3','null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'- базовая часть заработной платы учителей','f9f','trub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'- гарантированные доплаты учителей','f10f','trub');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (11,'- стимулирующие выплаты учителей','f11f','trub');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (12,'Наличие действующей региональной НСОТ, обеспечивающей действие всех установленных принципов','f1_1','bool','func_1_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (12,'Степень реализации принципов НСОТ (см. показатели 1.1.2-1.1.15 в таблице Текущие показатели)','f1_1a','count','func_1_1a');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (12,'Рост номинального значения средней зарплаты учителя за счет введения НСОТ по отношению к уровню 2006 года (по образовательным учреждениям, перешедшим на НСОТ) ','f1_2','proc','func_1_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (12,'Фактическая доля оплаты труда учителей в общем фонде оплаты труда работников общеобразовательных учреждений в среднем по субъекту РФ ','f1_3','proc','func_1_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (12,'Доля муниципальных образований, общеобразовательные учреждения в которых перешли на НСОТ','f1_4','proc','func_1_4');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (12,'Доля государственных и муниципальных общеобразовательных учреждений, которые перешли на НСОТ, от общего числа государственных и муниципальных общеобразовательных учреждений в субъекте РФ','f1_5','proc','func_1_5');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (13,'Доля муниципальных образований, общеобразовательные учреждения которых получают бюджетные средства на основе принципов НПФ, от общего числа муниципальных образований в субъекте РФ ','f2_1','proc','func_2_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (13,'Доля государственных и муниципальных общеобразовательных учреждений, получающих бюджетные средства на основе принципов НПФ, от общего числа общеобразовательных учреждений в субъекте РФ ','f2_2','proc','func_2_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (13,'Доля общеобразовательных учреждений, ведущих бухгалтерский и налоговый учет самостоятельно от общего числа общеобразовательных учреждений ','f2_3','proc','func_2_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (13,'Относительное (к 2006 году) повышение величины нормативов подушевого финансирования для государственных и муниципальных общеобразовательных учреждений, расположенных в городской местности, в соответствии с нормативными правовыми актами субъекта РФ ','f2_4_1','proc','func_2_4_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (13,'Относительное (к 2006 году) повышение величины нормативов подушевого финансирования для государственных и муниципальных общеобразовательных учреждений, расположенных в сельской местности, в соответствии с нормативными правовыми актами субъекта РФ ','f2_4_2','proc','func_2_4_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (13,'Фактическая доля учебных расходов в общем объеме бюджетного финансирования общеобразовательных учреждений на зарплату учителей и учебные расходы в среднем по региону','f2_5','proc','func_2_5');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (14,'Количество учебных предметов, по которым выпускникам XI (XII) классов общеобразовательных учреждений предоставляется возможность прохождения государственной (итоговой) аттестации в форме ЕГЭ ','f3_2','unit','func_3_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (14,'Доля выпускников XI (XII) классов общеобразовательных учреждений, проходящих государственную (итоговую) аттестацию по русскому языку и математике в форме ЕГЭ ','f3_3','proc','func_3_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (14,'Доля выпускников общеобразовательных учреждений, сдающих ЕГЭ по трем и более учебным предметам','f3_4','proc','func_3_4');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (14,'Доля выпускников IX классов общеобразовательных учреждений, проходящих государственную (итоговую) аттестацию по русскому языку и математике по новой форме','f3_5','proc','func_3_5');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (15,'Средняя наполняемость классов в общеобразовательных учреждениях, расположенных в городской местности','f4_2','man','func_4_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (15,'Средняя наполняемость классов в общеобразовательных учреждениях, расположенных в сельской местности','f4_3','man','func_4_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (15,'Число обучающихся на старшей ступени в расчете на одно общеобразовательное учреждение, имеющее старшую ступень','f4_4','man','func_4_4');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (16,'Доля общеобразовательных учреждений, в которых согласно зарегистрированному уставу создан и действует орган государственно-общественного управления (совет), обладающий комплексом управленческих полномочий, в том числе, по распределению средств стимулирующей части фонда оплаты труда общеобразовательного учреждения ','f5_1','proc','func_5_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (16,'Доля муниципальных образований, имеющих орган государственно-общественного управления образованием, ориентированный на его развитие, в том числе обладающий полномочиями по распределению фонда стимулирования руководителей общеобразовательных учреждений ','f5_2','proc','func_5_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (16,'Доля общеобразовательных учреждений, имеющих опубликованный (в СМИ, отдельным изданием, в сети Интернет) публичный отчет об образовательной и финансово-хозяйственной деятельности ','f5_3','proc','func_5_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (16,'Доля общеобразовательных учреждений, имеющих свои регулярно (не реже 2 раз в месяц) обновляемые сайты в сети Интернет','f5_4','proc','func_5_4');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (16,'Доля пунктов проведения итоговой аттестации выпускников общеобразовательных учреждений в форме ЕГЭ, в которых присутствовали общественные наблюдатели ','f5_5','proc','func_5_5');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (16,'Доля общеобразовательных учреждений, в лицензировании, аккредитации которых приняли участие общественные эксперты (от числа всех общеобразовательных учреждений, проходивших лицензирование, аккредитацию за отчетный период) ','f5_6','proc','func_5_6');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Принятие нормативного акта органа власти субъекта РФ, утверждающего содержание и запускающего реализацию РКП','fd1_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Предусмотрение в региональном бюджете средств на реализацию РКП на 2007 год','fd2_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Создание региональной рабочей группы (РРГ) по реализации РКП','fd3_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Создание регионального общественного совета (РОС) при высшем должностном лице субъекта РФ по реализации РКП ','fd4_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Заключение соглашений с муниципальными образованиями, участвующими в реализации РКП в 2007 году','fd5_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Публичная презентация и организация общественного обсуждения запуска РКП','fd6_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Создание регионального сайта РКП и его наполнение материалами, включая все принятые нормативные акты по реализации РКП, информацию об обязательствах региона по основным направлениям РКП, состав и контакты РРГ и РОС','fd7_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Объявление конкурсов на поставку товаров, выполнение работ, оказание услуг','fd8_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'1 транш','fd8_0');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'2 транш','fd8_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'3 транш','fd8_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Заключение контрактов на поставку товаров, выполнение работ, оказание услуг:','fd9_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'1 транш','fd9_0');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'2 транш','fd9_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'3 транш','fd9_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Завершение контрактов на поставку товаров, выполнение работ, оказание услуг','fd10_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Принятие нормативного акта субъекта РФ о введении НСОТ','fd11_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Разработка инструктивно-методических материалов по введению НСОТ','fd12_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Обучение управленческих команд (включая руководителя, финансиста, представителя управляющего совета) образовательных учреждений, в которых вводится НСОТ с 2007 года, по вопросам финансово-хозяйственной самостоятельности ОУ и введения НСОТ в условиях НПФ','fd13_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Мониторинг в муниципальных образованиях: расходования средств, переданных муниципальным образованиям в рамках внедрения РКП уровня социальной напряженности (в пилотных территориях по введению НСОТ)','fd15_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Подготовка и размещение на региональном сайте РКП аналитических материалов о запуске РКП, включая анализ мероприятий по предупреждению и снятию социальной напряженности','fd16_');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field)  VALUES (17,'Заседание РОС: рассмотрение хода реализации РКП в 2007 г.','fd17_');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (18,'Количество учащихся','f0_1r','man','func_0_1r');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (18,'Количество работников в ОУ','f0_3r','man','func_0_3r');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (18,'Количество учителей','f0_4_0r','man','func_0_4_0r');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (18,'Количество общеобразовательных учреждений в регионе, участвующих в проекте *','f0_5r','item','func_0_5r');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (18,'Количество сельских общеобразовательных учреждений, участвующих в проекте','f0_8_0r','item','func_0_8_0r');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (18,'Количество городских общеобразовательных учреждений, участвующих в проекте','f0_8_1r','item','func_0_8_1r');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (18,'Количество муниципальных образований','f0_6r','item','func_0_6r');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'Наличие нормативной правовой базы субъекта РФ о введении НСОТ (по результатам федеральной экспертизы) *','f1_1_1','bool','func_1_1_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'Степень реализации принципов НСОТ (по результатам федеральной экспертизы)','f1_1a','count','func_1_1a');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'1) Разделение оплаты труда педагогических работников системы общего образования на базовую и стимулирующую части, нормативно установленное на региональном уровне (по результатам федеральной экспертизы) *','f1_1_2','bool','func_1_1_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'2) Регламентация на региональном уровне учета в базовой части оплаты труда учителей интенсивности труда (численности обучающихся в учебных группах) (по результатам федеральной экспертизы) *','f1_1_3','bool','func_1_1_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'3) Регламентация на региональном уровне учета в базовой части оплаты труда учителей особенностей образовательных программ (в т.ч. сложность, приоритетность предмета, профильное обучение, углубленное обучение и т.п.) (по результатам федеральной экспертизы) *','f1_1_4','bool','func_1_1_4');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'4) Подготовка к урокам и другим видам учебных занятий (по результатам федеральной экспертизы) *','f1_1_5','bool','func_1_1_5');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'5) Проверка письменных работ (по результатам федеральной экспертизы) *','f1_1_6','bool','func_1_1_6');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'6) Изготовление дидактического материала и инструктивно-методических пособий (по результатам федеральной экспертизы) *','f1_1_7','bool','func_1_1_7');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'7) Консультации и дополнительные занятия с обучающимися (по результатам федеральной экспертизы) *','f1_1_8','bool','func_1_1_8');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'8) Классное руководство (по результатам федеральной экспертизы) *','f1_1_9','bool','func_1_1_9');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'9) Заведование элементами инфраструктуры (кабинетами, лабораториями, учебно-опытными участками и т.п.) (по результатам федеральной экспертизы) *','f1_1_10','bool','func_1_1_10');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'10) Работа с родителями (по результатам федеральной экспертизы) *','f1_1_11','bool','func_1_1_11');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'11) Наличие регионального регламента распределения стимулирующей части оплаты труда учителей (по результатам федеральной экспертизы) *','f1_1_12','bool','func_1_1_12');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'12) Предусмотренное региональным регламентом участие общественных советов в распределении стимулирующей части оплаты труда учителей (по результатам федеральной экспертизы) *','f1_1_13','bool','func_1_1_13');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'13) Наличие регионального регламента распределения стимулирующей части оплаты труда руководителей ОУ (по результатам федеральной экспертизы) *','f1_1_14','bool','func_1_1_14');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'14) Предусмотренное региональным регламентом участие общественных советов в распределении стимулирующей части оплаты труда руководителей ОУ (по результатам федеральной экспертизы) *','f1_1_15','bool','func_1_1_15');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'Номинальное значение средне-месячной зарплаты учителей (по образовательным учреждениям, перешедшим на НСОТ). (В текущем месяце отражаются показатели по зарплатам, выплаченным в течение предыдущего месяца)','f1_2','trub','func_1_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'Фактическая доля фонда оплаты труда учителей в общем фонде оплаты труда работников общеобразовательных учреждений в среднем по субъекту РФ. (В текущем месяце отражаются показатели по ФОТ учителей за предыдущий месяц)','f1_3','proc','func_1_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'Доля муниципальных образований, общеобразовательные учреждения в которых перешли на НСОТ','f1_4','proc','func_1_4');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (19,'Доля государственных и муниципальных общеобразовательных учреждений, которые перешли на НСОТ, от общего числа государственных и муниципальных общеобразовательных учреждений в субъекте РФ','f1_5','proc','func_1_5');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (20,'Количество учреждений, получающих финансирование по нормативу','f2_1r','item','func_2_1r');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (20,'Количество учреждений, имеющих финансовую самостоятельность','f2_3r','item','func_2_3r');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (20,'Величина норматива на городского школьника *','f2_4_1','trub','func_2_4_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (20,'Величина норматива на сельского школьника *','f2_4_2','trub','func_2_4_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (20,'Наличие нормативного правового акта субъекта РФ, устанавливающего нормативы бюджетного финансирования общеобразовательных учреждений на реализацию госстандарта (зарплату и учебные расходы) *','f2_5r','bool','func_2_5r');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Количество учебных предметов, по которым выпускникам XI (XII) классов общеобразовательных учреждений (текущего года) предоставляется возможность прохождения государственной (итоговой) аттестации в форме ЕГЭ. Число предметов выставляется по результатам федеральной экспертизы. *','f3_2','unit','func_3_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Доля (количество*) выпускников XI (XII) классов общеобразовательных учреждений, прошедших государственную (итоговую) аттестацию в форме ЕГЭ и по русскому языку, и по математике','f3_3','proc','func_3_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Доля (количество*) выпускников XI (XII) классов общеобразовательных учреждений, прошедших государственную (итоговую) аттестацию по русскому языку в форме ЕГЭ','f3_3_1','proc','func_3_3_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Доля (количество*) выпускников XI (XII) классов общеобразовательных учреждений, прошедших государственную (итоговую) аттестацию по математике в форме ЕГЭ','f3_3_2','proc','func_3_3_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Доля (количество*) выпускников общеобразовательных учреждений, сдающих ЕГЭ по трем и более учебным предметам','f3_4','proc','func_3_4');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Доля (количество*) выпускников IX классов общеобразовательных учреждений, прошедших государственную (итоговую) аттестацию по русскому языку и математике по новой форме','f3_5','proc','func_3_5');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Доля (количество*) выпускников IX классов общеобразовательных учреждений, прошедших государственную (итоговую) аттестацию по русскому языку в новой форме','f3_5_1','proc','func_3_5_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Доля (количество*) выпускников IX классов общеобразовательных учреждений, прошедших государственную (итоговую) аттестацию по математике в новой форме','f3_5_2','proc','func_3_5_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Проведение на региональном уровне мониторинговых исследований качества образования (по результатам федеральной экспертизы) (по результатам федеральной экспертизы) *','f3_6_1','bool','func_3_6_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Наличие структуры (организации), на базе которой обрабатываются и обобщаются результаты оценки качества образования (ЕГЭ, новая форма государственной (итоговой) аттестации выпускников IX классов общеобразовательных учреждений, аккредитация общеобразовательных учреждений, мониторинговые исследования и др.) (по результатам федеральной экспертизы)(по результатам федеральной экспертизы) *','f3_6_2','bool','func_3_6_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Наличие Положения о РСОКО (по результатам федеральной экспертизы) *','f3_7_1','bool','func_3_7_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Наличие документов, определяющих регламент функционирования РСОКО (по результатам федеральной экспертизы) *','f3_7_2','bool','func_3_7_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (21,'Наличие нормативных правовых документов, предусматривающих учет при оценке качества образования внеучебных достижений учащихся (по результатам федеральной экспертизы) *','f3_8','bool','func_3_8');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (22,'Доля (количество) школьников, обучающихся в общеобразовательных учреждениях, в которых директор имеет управленческую подготовку, подтвержденную документами о профессиональном образовании (специальность менеджер) и/или профессиональной переподготовке (квалификация менеджер)','f4_1a','proc','func_4_1a');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (22,'Средняя наполняемость классов в общеобразовательных учреждениях, расположенных в городской местности','f4_2','man','func_4_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (22,'Средняя наполняемость классов в общеобразовательных учреждениях, расположенных в сельской местности','f4_3','man','func_4_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (22,'Число обучающихся на старшей ступени в расчете на одно общеобразовательное учреждение, имеющее старшую ступень','f4_4','man','func_4_4');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (23,'Доля (количество) общеобразовательных учреждений, в которых согласно зарегистрированному уставу создан и действует орган государственно-общественного управления (совет), обладающий комплексом управленческих полномочий, в том числе, по распределению средств стимулирующей части фонда оплаты труда общеобразовательного учреждения','f5_1','proc','func_5_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (23,'Доля (количество) муниципальных образований, имеющих орган государственно-общественного управления образованием, ориентированный на его развитие, в том числе обладающий полномочиями по распределению фонда стимулирования руководителей','f5_2','proc','func_5_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (23,'Доля (количество) общеобразовательных учреждений, имеющих опубликованный (в СМИ, отдельным изданием, в сети Интернет) публичный отчет об образовательной и финансово-хозяйственной деятельности','f5_3','proc','func_5_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (23,'Доля (количество) общеобразовательных учреждений, имеющих свои регулярно (не реже 2 раз в месяц) обновляемые сайты в сети Интернет','f5_4','proc','func_5_4');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (23,'Количество пунктов проведения итоговой аттестации выпускников старшей ступени общего образования в форме ЕГЭ *','f5_5_1','item','func_5_5_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (23,'Доля (количество) пунктов проведения итоговой аттестации выпускников старшей ступени общего образования в форме ЕГЭ, в которых присутствовали общественные наблюдатели *','f5_5_2','proc','func_5_5_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (23,'Доля общеобразовательных учреждений, в лицензировании, аккредитации которых приняли участие общественные эксперты (от числа всех общеобразовательных учреждений, проходивших лицензирование, аккредитацию за текущий месяц)','f5_6','proc','func_5_6');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'Наличие комплекса инструктивно-методических материалов для муниципальных и школьных управленческих команд по направлениям РКП: *','f6_2','bool','func_6_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'- Введению НСОТ *','f6_2_1','bool','func_6_2_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'- Переходу на НПФ *','f6_2_2','bool','func_6_2_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'- Развитию РСОКО *','f6_2_3','bool','func_6_2_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'- Развитию сети *','f6_2_4','bool','func_6_2_4');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'- Расширению общественного участия в управлении образованием *','f6_2_5','bool','func_6_2_5');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'Наличие программ подготовки для муниципальных и школьных управленческих команд по направлениям РКП: *','f6_3','bool','func_6_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'- Введению НСОТ *','f6_3_1','bool','func_6_3_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'- Переходу на НПФ *','f6_3_2','bool','func_6_3_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'- Развитию РСОКО *','f6_3_3','bool','func_6_3_3');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'- Развитию сети *','f6_3_4','bool','func_6_3_4');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'- Расширению общественного участия в управлении образованием *','f6_3_5','bool','func_6_3_5');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'Доля (количество) общеобразовательных учреждений, управленческие команды которых прошли подготовку по каждому направлению РКП','f6_3_6','proc','func_6_3_6');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'Доля (количество) общеобразовательных учреждений, участвующих в электронном мониторинге реализации РКП','f6_4_2','proc','func_6_4_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'Доля (количество) муниципальных образований, управленческие команды которых прошли подготовку по каждому направлению РКП','f6_3_7','proc','func_6_3_7');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'Доля (количество) муниципальных образований, участвующих в электронном мониторинге реализации РКП','f6_4_1','proc','func_6_4_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (24,'Доля (количество) муниципальных образований, с которыми заключены соглашения на реализацию РКП','f6_5','proc','func_6_5');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (25,'Количество учащихся (текущее) ','f0_1u','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (25,'Количество классов (текущее) ','f0_2u','item');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (25,'Количество работников в ОУ (текущее) ','f0_3u','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (25,'Количество учителей (текущее) ','f0_4u','man', 'func_f0_4u');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (25,'Годовой бюджет учреждения ','f0_5u','trub', 'func_f0_5u');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (25,'ФОТ учреждения ','f0_6u','trub', 'func_f0_6u');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (25,'ФОТ учителей учреждения ','f0_7u','trub', 'func_f0_7u');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (25,'Является учреждение городским или сельским ','f0_8u','gets');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (25,'Текущее количество учащихся IX классов ','f0_9u','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (25,'Текущее количество учащихся X классов (или X и XI классов для вечерних учреждений) ','f0_10u','man');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (25,'Текущее количество учащихся XI классов (или XII классов для вечерних школ) ','f0_11u','man');

INSERT INTO prefix_monit_razdel_field (razdelid, name, calcfunc)  VALUES (26,'Фактическая доля фонда оплаты труда учителей в общем фонде оплаты труда работников общеобразовательных учреждений','portion_f1_3u');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (26,'Перешло ли учреждение на НСОТ ','f1_5u','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (26,'Реквизиты документа: ','f1_5g2','text');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (26,'Ссылка на документ: ','f1_5g3','link');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (27,'Получает ли учреждение финансирование по нормативу?','f2_1u','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (27,'Имеет ли учреждение финансовую самостоятельность?','f2_3u','bool');

INSERT INTO prefix_monit_razdel_field (razdelid, name, calcfunc)  VALUES (28,'Имеет ли учреждение лицензию на образовательную деятельность?', 'func_4_4_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, calcfunc)  VALUES (28,'Имеет ли учреждение свидетельство об аккредитации?', 'func_4_4_2');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (28,'Полная обеспеченность предметов федерального компонента базисного учебного плана учителями в соответствии со специальностью (квалификацией), что подтверждается документом о профессиональном образовании или профессиональной переподготовке ','f4_1_8','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (28,'Обеспеченность программ профильного обучения и предпрофильной подготовки учителями не ниже 2 квалификационной категории ','f4_1_9','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, calcfunc)  VALUES (28,'Наличие у директора ОУ управленческой подготовки, подтвержденной документами о профессиональном образовании (специальность менеджер) и/или профессиональной переподготовке (квалификация менеджер)', 'func_4_1_11');

INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (29,'Есть ли в учреждении согласно зарегистрированному уставу орган государственно-общественного управления (совет), обладающий комплексом управленческих полномочий, в том числе, по распределению средств стимулирующей части фонда оплаты труда ','f5_1u','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (29,'Реквизиты документа: ','f5_1g2','text');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (29,'Ссылка на документ: ','f5_1g3','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (29,'Имеет ли учреждение опубликованный публичный отчет об образовательной и финансово-хозяйственной деятельности за прошедший учебный год? ','f5_3_0u','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (29,'Ссылка на отчет: ','f5_3g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (29,'Имеет ли образовательное учреждение собственный сайт в сети Интернет ','f5_4_1u','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (29,'Ссылка на сайт: ','f5_4g','link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, calcfunc)  VALUES (29,'Проходило ли учреждение лицензирование или аккредитацию в текущем месяце?', 'func_5_6_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, calcfunc)  VALUES (29,'Принимали ли участие в лицензировании и аккредитации учреждения общественные эксперты?', 'func_5_6_2');

INSERT INTO prefix_monit_razdel_field (razdelid, name, calcfunc)  VALUES (30, 'Имеет ли учреждение оператора, сопровождающего мониторинг КПМО?', 'func_6_0');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm, calcfunc)  VALUES (30, 'Проведение общественного обсуждения плана участия общеобразовательного учреждения в РКП','fd_u_1','expl', 'func_d_u_1');
INSERT INTO prefix_monit_razdel_field (razdelid, name, edizm, calcfunc)  VALUES (30, 'Реквизиты документа: ', 'text', 'func_d_u_1_doc');
INSERT INTO prefix_monit_razdel_field (razdelid, name, edizm, calcfunc)  VALUES (30, 'Ссылка на документ: ', 'link', 'func_d_u_1_link');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (30, 'Прошла ли управленческая команда учреждения (руководитель, финансист, общественный управляющий) подготовку по: ', 'f6_3_6u', 'null');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (30,'Введению НСОТ','f6_3_6_1','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (30,'Переходу на НПФ','f6_3_6_2','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (30,'Развитию РСОКО','f6_3_6_3','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (30,'Развитию сети','f6_3_6_4','bool');
INSERT INTO prefix_monit_razdel_field (razdelid, name, name_field, edizm)  VALUES (30,'Расширению общественного участия в управлении образованием','f6_3_6_5','bool');

