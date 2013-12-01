<?php
if ( !isset($website ) ) { header('HTTP/1.1 404 Not Found'); die; }

$lang = array();

$lang["home"]       = "Početak";
$lang["top"]        = "Top Igrači";
$lang["game_archive"]  = "Istorija igara";
$lang["media"]      = "Ostalo";
$lang["guides"]     = "Tutorijal";
$lang["heroes"]     = "Heroji";
$lang["heroes_vote"]= "Heroji glasanje";
$lang["item"]       = "Predmet";
$lang["items"]      = "Predmeti";
$lang["bans"]       = "Banovi";
$lang["all_bans"]   = "Svi banovi";
$lang["ban_report"] = "Prijava bana";
$lang["ban_appeal"] = "Žalba na ban";
$lang["report_user"] = "Prijavi korisnika";
$lang["warn"]       = "Upozorenja";
$lang["warned"]     = "Upozoren";
$lang["expire"]     = "Ističe";
$lang["expires"]    = "Ističe";
$lang["expired"]    = "Istekao";
$lang["search"]     = "Pretraga";
$lang["search_players"] = "Pretraga igraač...";
$lang["admins"]     = "Admini";
$lang["safelist"]   = "Sigurna lista";
$lang["about_us"]   = "O Nama";
$lang["members"]    = "Članovi";
$lang["live_games"]      = "Igre Uživo";
$lang["select_country"]  = "Izaberi Zemlju";

$lang["username"]   = "Korisničko ime";

//ROLES
$lang["member_admin"]          = "Admin";
$lang["member_root"]           = "Root";
$lang["member_reserved"]       = "Vaučer";
$lang["member_safe"]           = "Rezervisan slot";
$lang["member_safe_reserved"]  = "Na safelist-i";
$lang["member_web_moderator"]  = "Website Moderator";
$lang["member_bot_moderator"]  = "Simple Bot Moderator";
$lang["member_bot_full_mod"]   = "Full Bot Moderator";
$lang["member_global_mod"]     = "Global Moderator";
$lang["member_bot_hoster"]     = "Hoster";

$lang["sort_by_role"]      = "Sort by role";
$lang["sort_by_bnet"]      = "Sort by bnet";
$lang["sort_by_default"]   = "Sort by default";

$lang["recent_games"]   = "Poslednje igre";
$lang["recent_news"]    = "Poslednje vesti";

$lang["profile"]         = "Profil";
$lang["admin_panel"]     = "Admin Panel";
$lang["logout"]          = "Odjavi se &times; ";
$lang["login_register"]  = "Prijava/Registracija";
$lang["login_fb_info"]   = "Klikni na dugme iznad da se prijaviš sa tvojim FB nalogom";
$lang["total_comments"]  = "komentar(a)";
$lang["succes_registration"]  = "Uspešno ste se registrovali";

$lang["profile_changed"]  = "Profil je uspešno ažuriran";
$lang["password_changed"] = "Lozinka je uspešno promenjena";

$lang["bnet_welcome_email_subject"] = "Registracija uspešna - Bnet nalog";
$lang["bnet_welcome_email"] = "Uspešno ste registrovali vaš Battle.net nalog na našem sajtu.<br />Kliknite na link ispod da potvrdite vašu email adresu.<br />";
$lang["bnet_mystats"]     = "Pregled statistike";

$lang["unconfirmed_email"]     = "Email nije potvrđen";

//APPEAL
$lang["verify_appeal"]      = "Provera Banovanog Naloga";
$lang["verify_appeal_info"] = "Upiši ime ispod kako bi proverili da li korisnik banovan";
$lang["appeal_here"]        = "Možeš se žaliti na ovaj ban ";
$lang["here"]               = "ovde";
$lang["you_must_be"]        = "Potrebno je da se";
$lang["logged_in"]          = "prijaviš";
$lang["to_appeal"]          = "kako bi napisao žalbu";
$lang["to_use_feature"]     = "da bi koristili ovu opciju";
$lang["appeal_ban_date"]    = "Ovaj nalog je banovan";
$lang["was_banned"]         = "banovao je";
$lang["appeal_for"]         = "Žalba na ban za korisnika";
$lang["subject"]            = "Naslov";
$lang["your_message"]       = "Tvoja poruka ovde";
$lang["game_url"]           = "URL adresa igre";
$lang["replay_url"]         = "URL adresa za replay";

$lang["appeal_info1"] = "Tvoj battle.net nalog:";
$lang["appeal_info2"] = "Ukoliko si banovan ovde možeš napisati žalbu. Da bi napisao žalbu potrebno je da tvoj nalog bude verifikovan na battle.net-u.";
$lang["appeal_info3"] = "Pomoć?";
$lang["appeal_info4"] = "Idi na";
$lang["appeal_info5"] = "tvoj profil";
$lang["appeal_info6"] = "gde možeš videti uputstvo za potvrdu naloga.";
$lang["appeal_info7"] = "Verifikovani nalozi imaju battle.net ikonicu pored korisničkog imena:";

//REPORT
$lang["report_player"]      = "Prijavi igrača";
$lang["report_reason"]      = "Razlog prijave igrača+";
$lang["report_submit"]      = "Dodaj prijavu";
$lang["error_report_player"]   = "Naziv igrača nema dovoljno znakova";
$lang["error_report_subject"]  = "Naslov nema dovoljno znakova";
$lang["error_report_reason"]   = "Tekst nema dovoljno znakova";
$lang["error_no_player"]       = "Nema podataka u bazi za ovog igrača";
$lang["error_already_banned"]  = "Taj korisnik je već banovan"; 
$lang["error_report_login"]    = "Potrebno je da se prijaviš da bi napisao prijavu";
$lang["error_report_time"]     = "Molimo sačekajte. Ne možete tako brzo pisati prijave.";
$lang["error_report_time2"]    = "Molimo sačekajte. Ne možete tako brzo pisati žalbe.";

$lang["appeal_successfull"]    = "Žalba uspešno dodata.";
$lang["report_successfull"]    = "Prijava uspešno dodata.";

//Time played. Ex. 10h 23m 16s
$lang["h"]             = "h ";
$lang["m"]             = "m ";
$lang["s"]             = "s ";

//Hero stats
$lang["time_played"]             = "Vreme igranja";
$lang["average_loading"]         = "Prosečno vreme učitavanja";
$lang["total_loading"]           = "Ukupno vreme učitavanja";
$lang["seconds"]                 = "sek.";
$lang["s"]                       = "s"; //seconds short
$lang["favorite_hero"]           = "Omiljeni heroji:";
$lang["most_wins"]               = "Najviše pobeda:";
$lang["played"]                  = "Igrano";

//Seconds
$lang["error_sec"]             = "sek.";

$lang["game"]       = "Igra";
$lang["duration"]   = "Trajanje";
$lang["type"]       = "Tip";
$lang["date"]       = "Datum";
$lang["map"]        = "Mapa";
$lang["creator"]    = "Napravio";

$lang["hero"]    = "Heroj";
$lang["player"]  = "Igrač";
$lang["kda"]     = "K/D/A";
$lang["cdn"]     = "C/D/N";
$lang["trc"]     = "T/R/C";
$lang["gold"]    = "Zlato";
$lang["left"]    = "Napustio";
$lang["sent_winner"]    = "Sentinel Pobedio";
$lang["scou_winner"]    = "Scourge Pobedio";
$lang["sent_loser"]     = "Sentinel Izgubio";
$lang["scou_loser"]     = "Scourge Izgubio";
$lang["draw_game"]      = "Nerešena igra";

$lang["most_kills"]      = "Najviše Ubistva:";
$lang["most_assists"]    = "Najviše Asistencija:";
$lang["most_deaths"]     = "Najviše Smrti:";
$lang["top_ck"]          = "Top Creep Kills:";
$lang["top_cd"]          = "Top Creep Denies:";

$lang["score"]    = "Bodovi";
$lang["games"]    = "Igre";
$lang["wld"]     = "W/L/D";
$lang["wl"]     = "W/L";
$lang["tr"]     = "T/R";

$lang["sortby"]     = "Sortiraj:";
$lang["wins"]       = "Pobede";
$lang["losses"]     = "Porazi";
$lang["draw"]       = "Nerešeno";

$lang["kills"]       = "Ubistva";
$lang["player_name"] = "Naziv igrača";
$lang["deaths"]      = "Smrti";
$lang["assists"]     = "Asistencijre";
$lang["ck"]          = "Creep Kills";
$lang["cd"]          = "Creep Denies";
$lang["nk"]          = "Neutral Kills";

$lang["towers"]      = "Kule";
$lang["rax"]         = "Rax";
$lang["neutrals"]    = "Neutrals";
$lang["submit"]      = "Sačuvaj";

$lang["page"]          = "Strana";
$lang["pageof"]        = "od";
$lang["total"]         = "ukupno";
$lang["next_page"]     = "Sledeća strana";
$lang["previous_page"] = "Prethodna strana";

$lang["fastest_game"]   = 'Najbrža pobeda u igri';
$lang["longest_game"]   = 'Najduža pobeda u igri';

$lang["game_history"]         = "Istorijat igara:";
$lang["user_game_history"]    = "Istorijat igara igrača";
$lang["best_player"]          = "Najbolji igrač: ";
$lang["show_hero_history"]    = "Prika.i sve igre sa ovim herojem";

$lang["download_replay"]      = "Download replay";
$lang["view_gamelog"]         = "Pogledaj Gamelog";

$lang["win_percent"]          = "Pobede %";
$lang["wl_percent"]           = "W/L%";
$lang["kd_ratio"]             = "K/D Odnos";
$lang["kd"]                   = "K/D";
$lang["kpg"]                  = "KPG";
$lang["kills_per_game"]       = "Ubistva po igri";
$lang["dpg"]                  = "DPG";
$lang["apg"]                  = "APG";
$lang["assists_per_game"]     = "Asistencija po igri";
$lang["ckpg"]                 = "CKPG";
$lang["creeps_per_game"]      = "Ubijeno krepova po igri";
$lang["cdpg"]                 = "CDPK";
$lang["denies_per_game"]      = "Denies per game";
$lang["deaths_per_game"]      = "Deaths per game";
$lang["npg"]                  = "NPG";
$lang["neutrals_per_game"]    = "Neutrals per game";
$lang["search_results"]       = "Rezultati pretrage za: ";
$lang["user_not_found"]       = "Korisnik nije pronađen";
$lang["left_info"]            = "Koliko puta je igrač napustio igru pre vremena";

$lang["admin"]       = "Admin";
$lang["server"]      = "Server";
$lang["voucher"]     = "Vaučer";
$lang["role"]        = "Uloga";  

$lang["banned"]     = "BANOVAN";
$lang["permanent_ban"]   = "Permanentno";
$lang["reason"]     = "Razlog";
$lang["game_name"]  = "Naziv igre";
$lang["bannedby"]   = "Banovao";
$lang["leaves"]     = "Izašao";
$lang["stayratio"]     = "Stay ratio";
$lang["leaver"]        = "Leaver";
$lang["streak"]        = "Streak";
$lang["longest_streak"]= "Uzastopne pobede";
$lang["losing_streak"] = "Uzastopni porazi";
$lang["zero_deaths"]   = "Ukupan broj igara gde igrač ima 0 smrti";

$lang["comments"]             = "Komentari";
$lang["latest_comments"]      = "Poslednji Komentari";
$lang["add_comment"]          = "Dodaj komentar";
$lang["add_comment_button"]   = "Dodaj komentar";
$lang["reply"]                = "[odgovor]";

$lang["error_comment_not_allowed"]   = "Pisanje komentara nije dozvoljeno za ovaj post";
$lang["error_invalid_form"]          = "Pogrešna forma";
$lang["error_text_char"]             = "Tekst nema dovoljno znakova";

$lang["gamestate_priv"]       = "PRIV";
$lang["gamestate_pub"]        = "PUB";

//Login / Registration
$lang["login"]       = "Prijava";
$lang["logged_as"]   = "Prijavljen kao ";
$lang["email"]       = "E-mail";
$lang["avatar"]      = "Avatar";
$lang["location"]    = "Lokacija";
$lang["realm"]       = "Realm";
$lang["website"]     = "Website";
$lang["gender"]      = "Pol";
$lang["male"]        = "Muško";
$lang["female"]      = "Žensko";
$lang["password"]    = "Lozinka";
$lang["register"]    = "Registracija";
$lang["username"]    = "Korisničko ime";
$lang["language"]    = "Jezik";
$lang["confirm_password"]       = "Potvrda lozinke";
$lang["change_password"]        = "Promena lozinke";
$lang["change_password_info"]   = "Označi ovo ukoliko menjaš lozinku";
$lang["comment_not_logged"]     = "Potrebno je da se prijaviš za pisanje komentara.";
$lang["acc_activated"]          = "Nalog je uspešno aktiviran. Sada možeš da se prijaviš.";
$lang["invalid_link"]           = "Link nije validan ili je istekao.";

//Heroes and items
$lang["hero"]   = "Heroj";
$lang["description"]     = "Opis";
$lang["stats"]           = "Stats";
$lang["skills"]          = "Skills";

$lang["search"]          = "Pretraga";
$lang["search_bans"]     = "Pretraga bana...";
$lang["search_members"]  = "Pretraga Članova...";
$lang["search_heroes"]   = "Pretraga heroja...";
$lang["search_items"]    = "Pretraga predmeta...";

//Errors
$lang["error_email"]      = "E-mail adresa nije ispravna";
$lang["error_email_banned"]      = "Email adresa je banovana";
$lang["error_short_pw"]   = "Polje lozinka nema dovoljno znakova";
$lang["error_passwords"]  = "Lozinke se ne poklapaju";
$lang["error_inactive_acc"]   = "Nalog još uvek nije aktiviran";
$lang["error_invalid_login"]  = "Pogrešna email adresa ili lozinka";
$lang["error_short_un"]   = "Polje Korisničko ime nema dovoljno znakova";
$lang["error_un_taken"]   = "Korisničko ime već postoji";
$lang["error_username"]   = "Pogrešno korisničko ime";
$lang["error_email_taken"]= "E-mail adresa već postoji";
$lang["error_email_send"] = "E-mail je već poslat";

//Email
$lang["email_charset"] = "UTF-8";
$lang["email_subject_activation"] = "Aktivacija naloga";
$lang["email_from"] = "no-reply@ohsystem.net";
$lang["email_from_full"] = "OpenHosting";
$lang["email_send"] = "Email poruka je uspešno poslata";

//Email text
$lang["email_activation1"] = "Pozdrav";
$lang["email_activation2"] = "Uspešno si se registrovao na sajt ";
$lang["email_activation3"] = "Klikni na sledeći link da potvrdiš tvoju email adresu i aktiviraš nalog";

//GAME LOG
$lang["game_log"]            = "Game Log";
$lang["log_player"]          = "Igrač";
$lang["log_priv"]            = "[Priv]";
$lang["log_ally"]            = "[Allies]";
$lang["log_first_blood"]     = "for first blood";
$lang["log_suicide"]         = "has killed himself!";
$lang["log_denied_teammate"] = "denied his teammate";
$lang["log_level"]           = "level";
$lang["log_tower"]           = "tower";
$lang["log_barracks"]        = "barracks";

$lang["404_error"]           = "Ooops, stranica nije pronađena";

//VOTES
$lang["vote_title"]          = "Vote for favorite";
$lang["votes_won"]           = "Votes Won";
$lang["votes_lost"]          = "Votes Lost";
$lang["votes_total"]         = "Total Votes";
$lang["votes_best"]          = "Best";
$lang["vote_vs"]             = "VS";
$lang["vote_sort"]           = "Submit";
$lang["vote_results"]        = "Vote Results";
$lang["vote_back"]           = "Back to Vote";

$lang["vote_won"]           = "Won:";
$lang["vote_lost"]          = "Lost:";
$lang["vote_again"]         = "Vote again?";

$lang["vote_error1"]        = "Please click on the hero to vote.";
$lang["vote_display"]       = "Display results";

$lang["read_more"]          = "...[more]";

$lang["upload_image"]      = "Dodaj sliku";
$lang["remove_avatar"]     = "Ukloni avatara";

//Gamelist patch
$lang["current_games"]      = "Trenutne igre";
$lang["refresh"]            = "Osveži";
$lang["slots"]              = "Slotovi / Ukupno";
$lang["empty"]              = "Prazno";
$lang["ms"]                 = "ms";
$lang["players"]            = "Igrači";

//Members
$lang["joined"]         = "Pridružio";
$lang["user_info"]      = "Info";

$lang["choose"]         = "Izaberi";
$lang["all_guides"]     = "---All guides---";

//Compare players
$lang["compare_back"]         = "&laquo; Nazad na Top listu";
$lang["compare_list"]         = "Lista";
$lang["compare_list_empty"]   = "Lista je prazna";
$lang["compare_compare"]      = "Uporedi";
$lang["compare_add"]          = "Dodaj u listu za upoređivanje";
$lang["compare_clear"]        = "Očisti listu";
$lang["compare_remove_player"]   = "Ukloni igrača iz liste?";
$lang["compare_players"]      = "UPOREDI IGRAČE";
$lang["compare_empty_info"]   = "Tvoja lista za upoređivanje igrača je prazna";
$lang["overall"]              = "Ukupno";
$lang["stay"]              = "Stay";

//Chat/GameLog
$lang["chat_attention"]     = "<b>Pažnja:</b> Nemmojte zloupotrebljavati ovu opciju i spamovati igrače, jer to može biti kažnjivo.";
$lang["chat_title_player"]  = "- pošalji poruku direktno igraču u igri";
$lang["chat_successfull"]   = "<b>Poruka je uspešno poslata</b>";
$lang["chat_error1"]        = "Poruka nema dovoljno znakova.";

$lang["gl_lobby"]    = "[Lobby]";
$lang["gl_system"]   = "[System]";
$lang["gl_bot"]      = "[BOT]";
$lang["gl_picked"]   = "uzeo";
$lang["gl_swap"]     = "želi da se zameni sa";
$lang["gl_mode"]     = "Mod je namešten:";
$lang["gl_heropick"] = "izabrao heroja";
$lang["gl_level"]    = "je sada nivo";
$lang["gl_assists"]  = "Assistencije:";
$lang["gl_killed"]   = "ubio";
$lang["gl_suicide"]  = "committed suicide.";
$lang["gl_used"]     = "iskoristio ";
$lang["gl_stored"]   = "stavio u bocu ";
$lang["gl_rune"]     = "runu.";
$lang["gl_killed_t"] = "ubio";
$lang["gl_tower"]    = "kulu.";
$lang["gl_rax"]      = "rax.";
$lang["gl_attack"]   = "napada";
$lang["gl_killed_by"]= "je ubio ";
$lang["gl_item"]     = "je upravo kupio";

$lang["gl_playerlist_sentinel_title"] = "Igrač (Nivo) - Sentinel";
$lang["gl_playerlist_scourge_title"] = "Igrač (Nivo) - Scourge";
$lang["gl_playerlist_kda"]        = "K/D/A";
$lang["gl_playerlist_send"]       = "Pošalji poruku igraču direktno u igri";
$lang["gl_playerlist_sentinel"]       = "Sentinel";
$lang["gl_playerlist_scourge"]        = "Scourge";
$lang["gl_game_started"]              = "Game started";

$lang["gl_compare_sentinel"] = "Player - Sentinel";
$lang["gl_compare_scourge"]  = "Player - Scourge";
$lang["gl_compare_wl"]       = "W/L";
$lang["gl_compare_perc"]     = "%";
$lang["gl_compare_wl_sentinel"]       = "Wins/Loses - SENTINEL";
$lang["gl_compare_sentinel_chance_t"]   = "Chance to win - SENTINEL";
$lang["gl_compare_sentinel_chance"]     = "Sentinel have a chance to win";

$lang["gl_compare_no"]     = "There are not enough players.";

$lang["gl_compare_wl_scourge"]         = "Wins/Loses - SCOURGE";
$lang["gl_compare_scourge_chance_t"]   = "Chance to win - SCOURGE";
$lang["gl_compare_scourge_chance"]     = "Scourge have a chance to win";

$lang["gl_calculate"]                  = "Calculate a chance to win";
$lang["gl_not_ranked"]                 = "not ranked yet";
$lang["gl_no_accesss"]                 = "Nemate pristup!";
$lang["gl_no_stats_data"]              = "Not enough statistical data";

$lang["gl_login"] = "Please <a href='".$website."?login'>login</a> to view the complete log";

$lang["member_comments"]     = "Komentari";
$lang["member_registered"]   = "Datum registracije";
$lang["member_location"]     = "Lokacija";
$lang["member_realm"]        = "Realm";
$lang["member_website"]      = "Sajt";
$lang["member_gender"]       = "Pol";
$lang["member_last_login"]   = "Poslednja prijava";
$lang["member_clan"]         = "Klan";

$lang["realm_username"]      = "Realm ime";
$lang["protected_account"]   = "Zaštićen nalog";
$lang["protect_account"]     = "Zaštita naloga";
$lang["acount_is_protected"] = "Tvoj nalog je zaštićen";
$lang["acount_is_not_protected"] = "Naslog nije zaštićen na Battle.net-u";
$lang["acount_protected_step"]   = "Još jedan korak pre nego što je tvoj nalog zaštićen";
$lang["set_password"]        = "Zaštiti sa lozinkom";
$lang["protect_info1"]       = "Možeš zaštititi tvoj nalog sa lozinkom.";
$lang["protect_info2"]       = "<b>!pw</b> <i>[lozinka]</i> - provera/verifikacija naloga u igri";
$lang["protect_info3"]       = "<b>!pw</b> <i>[lozinka] clear</i> - uklanjanje zaštite";
$lang["protect_info4"]       = "Detaljnije";
$lang["protect_info5"]       = "Na ovaj način tvoj nalog ne mogu da zloupotrebe drugi igrači (na primer da koriste tvoje ime)";
$lang["protect_info6"]       = "Korišćenje";
$lang["protect_info7"]       = "Ako je tvoja lozinka u polju iznad <b>mypassword</b>, možeš koristiti komandu";
$lang["protect_info8"]       = "<b>!pw mypassword</b> zaštita naloga";
$lang["protect_info9"]       = "<b>!pw mypassword clear</b> - uklanjanje zaštite ili";
$lang["protect_info10"]       = "<b>!pw mypassword 0</b> - uklanjanje zaštite";
$lang["protect_info11"]      = "<b>Pažnja:</b> Ako je lozinka postavljena i <b>nisi</b> iskoristio komandu <b>!pw</b> bićeš kikovan u roku od 30 sekundi";
$lang["protect_info12"]      = "<i>Lozinka ne sme sadržati razmake (prazna polja)</i>";

$lang["protect_info13"]      = "Napisao si lozinku, ali tvoj nalog još uvek nije potvrđen na battle.net-u";
$lang["protect_info14"]      = "Da bi potvrdio nalog koristi komandu <b>!confirm email password</b>";
$lang["protect_info15"]      = "<b>Primer:</b> Tvoj email je <b>example@mail.com</b> a lozinka je <b>password123</b>";
$lang["protect_info16"]      = "<b>Komanda:</b> !confirm example@mail.com password123";
$lang["protect_info17"]      = "Verifikovani nalozi imaju 2 ikonice: ";

$lang["protect_info18"]      = "<i>Ovo nije lozinka za tvoj nalog na sajtu, već lozinka za zaštitu naloga na Battle.net-u</i>";
$lang["protect_info19"]      = "<i>Ova lozinka se koristi isključivo za igranje na našim hosting botovima</i>";
$lang["protect_info20"]      = "<i>Kada koristiš komandu <b>!pw</b>, drugi korisnici ne mogu videti šta si kucao</i>";
$lang["protect_info21"]      = "Prikaži mi komandu";
$lang["protect_info22"]      = "Kada verifikuješ tvoj nalog, možeš ukloniti lozinku (ako hoćeš).";

//OFFENCES
$lang["player_offences"]     = "Prekršaji igrača";
$lang["player_warned"]       = "Ovaj igrač je upozoren";
$lang["has_total_of"]        = "puta i ima ukupno";
$lang["penalty_points"]      = "kaznenih poena";
$lang["pp"]                  = "KP";

$lang["points"]              = "Poeni";

$lang["points_info1"]        = "Upravo si dobio ";
$lang["points_info2"]        = "poena";
$lang["points_info3"]        = "za dnevnu prijavu na sajt";
$lang["points_info4"]        = "Pažnja: Samo rangirani igrači dobijaju poene";

$lang["hide_stats"]          = "Ne prikazuj moju statistiku";
$lang["show_stats"]          = "Prikaži moju statistiku";
$lang["hide_stats_info"]     = "Ako sakriješ statistiku, niko neće moći da vidi tvoje statističke podatke (osim kod pojedinačnih igara)<div>Moraš potvrditi tvoj Battle net nalog da bi menjao ovu opciju.</div>";
$lang["hide_stats_message"]  = "Korisnik ne želi da prikazuje svoju statistiku";
?>