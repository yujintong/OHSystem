<?php

// Translated by [v3rlaine]

if ( !isset($website ) ) { header('HTTP/1.1 404 Not Found'); die; }

$lang = array();

$lang["home"]       = "Home";
$lang["top"]        = "Top Players";
$lang["game_archive"]  = "Juegos";
$lang["media"]      = "Misc";
$lang["guides"]     = "Guias";
$lang["heroes"]     = "Heroes";
$lang["heroes_vote"]= "Heroes Vote";
$lang["item"]       = "Item";
$lang["items"]      = "Items";
$lang["bans"]       = "Bans";
$lang["all_bans"]   = "All Bans";
$lang["ban_report"] = "Ban Report";
$lang["ban_appeal"] = "Ban Appeal";
$lang["report_user"] = "Report user";
$lang["warn"]       = "Warns";
$lang["warned"]     = "Warned";
$lang["expire"]     = "Expire";
$lang["expires"]    = "Expires";
$lang["expired"]    = "Expired";
$lang["search"]     = "Search";
$lang["search_players"] = "Buscar jugadores...";
$lang["admins"]     = "Admins";
$lang["safelist"]   = "Safelist";
$lang["about_us"]   = "About Us";
$lang["members"]    = "Members";
$lang["live_games"]      = "Live Games";


$lang["username"]   = "Usuario";

//ROLES
$lang["member_admin"]          = "Admin";
$lang["member_root"]           = "root";
$lang["member_reserved"]       = "Vouched";
$lang["member_safe"]           = "Slot Reservado";
$lang["member_safe_reserved"]  = "Safelisted";
$lang["member_web_moderator"]  = "Website Moderator";
$lang["member_bot_moderator"]  = "Simple Bot Moderator";
$lang["member_bot_full_mod"]   = "Full Bot Moderator";
$lang["member_global_mod"]     = "Global Moderator";
$lang["member_bot_hoster"]     = "Hoster";

$lang["sort_by_role"]      = "Ordenar por role";
$lang["sort_by_bnet"]      = "Ordenar por bnet";
$lang["sort_by_default"]   = "Ordenar por defecto";

$lang["recent_games"]   = "Juegos Recientes";
$lang["recent_news"]    = "Noticias Recientes";

$lang["profile"]         = "Profile";
$lang["admin_panel"]     = "Admin Panel";
$lang["moderator_panel"] = "Moderator Panel";
$lang["logout"]          = "Logout &times; ";
$lang["login_register"]  = "Login/Register";
$lang["login_fb_info"]   = "Haga clic en el botón de arriba para iniciar sesión con tu cuenta de FB";
$lang["total_comments"]  = "comentario(s)";
$lang["succes_registration"]  = "Registrado correctamente";

$lang["profile_changed"]  = "El perfil ha sido actualizado correctamente";
$lang["password_changed"] = "La contraseña ha sido actualizada correctamente";

$lang["bnet_welcome_email_subject"] = "Registro de éxito - cuenta Bnet";
$lang["bnet_welcome_email"] = "Has registrado correctamente tu cuenta de Battle.net en nuestro sitio web.<br />Haga clic en el enlace de abajo para verificar su cuenta.<br />";
$lang["bnet_mystats"]     = "Ver Stats";

$lang["unconfirmed_email"]     = "Email sin confirmar";

//APPEAL
$lang["verify_appeal"]      = "Verificar usuario Baneado";
$lang["verify_appeal_info"] = "Escriba el nombre a continuación para comprobar si el usuario está baneado";
$lang["appeal_here"]        = "Usted puede apelar este ban ";
$lang["here"]               = "Aquí";
$lang["you_must_be"]        = "Usted debe ser";
$lang["logged_in"]          = "Conectado";
$lang["to_appeal"]          = "a la apelación";
$lang["to_use_feature"]     = "para utilizar esta función";
$lang["appeal_ban_date"]    = "Esta cuenta fue baneada en";
$lang["was_banned"]         = "was banned by";
$lang["appeal_for"]         = "Ban Appeal for user";
$lang["subject"]            = "Subject";
$lang["your_message"]       = "Your message here";
$lang["game_url"]           = "Game URL";
$lang["replay_url"]         = "Replay URL";

$lang["appeal_info1"] = "Your battle.net username:";
$lang["appeal_info2"] = "Here you can appeal if you are banned. To write an appeal, your account must be verified on battle net.";
$lang["appeal_info3"] = "Help?";
$lang["appeal_info4"] = "Go to";
$lang["appeal_info5"] = "your profile";
$lang["appeal_info6"] = "where you will see instructions for verifying profile.";
$lang["appeal_info7"] = "Verified accounts have battle.net icon next to the name:";

//REPORT
$lang["report_player"]      = "Report player";
$lang["report_reason"]      = "Reason for reporting player";
$lang["report_submit"]      = "Submit Report";
$lang["error_report_player"]   = "Player name does not have enough characters";
$lang["error_report_subject"]  = "Subject does not have enough characters";
$lang["error_report_reason"]   = "Text does not have enough characters";
$lang["error_no_player"]       = "No information about this player in our database";
$lang["error_already_banned"]  = "This player is already banned"; 
$lang["error_report_login"]    = "You need to login to write a report";
$lang["error_report_time"]     = "You can't quickly write ban reports. Please wait";
$lang["error_report_time2"]    = "You can't quickly write ban appeals. Please wait";

$lang["appeal_successfull"]    = "Appeal successfully added.";
$lang["report_successfull"]    = "Report successfully added.";

//Time played. Ex. 10h 23m 16s
$lang["h"]             = "h ";
$lang["m"]             = "m ";
$lang["s"]             = "s ";

//Hero stats
$lang["time_played"]             = "Time Played";
$lang["average_loading"]         = "Average loading time";
$lang["total_loading"]           = "Total loading time";
$lang["seconds"]                 = "sec.";
$lang["s"]                       = "s"; //seconds short
$lang["favorite_hero"]           = "Favorite Hero:";
$lang["most_wins"]               = "Most Wins:";
$lang["played"]                  = "Played";

//Seconds
$lang["error_sec"]             = "sec.";

$lang["game"]       = "Game";
$lang["duration"]   = "Duration";
$lang["type"]       = "Type";
$lang["date"]       = "Date";
$lang["map"]        = "Map";
$lang["creator"]    = "Creator";

$lang["hero"]    = "Hero";
$lang["player"]  = "Player";
$lang["kda"]     = "K/D/A";
$lang["cdn"]     = "C/D/N";
$lang["trc"]     = "T/R/C";
$lang["gold"]    = "Gold";
$lang["left"]    = "Left";
$lang["sent_winner"]    = "Sentinel Winner";
$lang["scou_winner"]    = "Scourge Winner";
$lang["sent_loser"]     = "Sentinel Loser";
$lang["scou_loser"]     = "Scourge Loser";
$lang["draw_game"]      = "Draw Game";

$lang["most_kills"]      = "Most Kills:";
$lang["most_assists"]    = "Most Assists:";
$lang["most_deaths"]     = "Most Deaths:";
$lang["top_ck"]          = "Top Creep Kills:";
$lang["top_cd"]          = "Top Creep Denies:";

$lang["score"]    = "Score";
$lang["games"]    = "Games";
$lang["wld"]     = "W/L/D";
$lang["wl"]     = "W/L";
$lang["tr"]     = "T/R";

$lang["sortby"]     = "Sort by:";
$lang["wins"]       = "Wins";
$lang["losses"]     = "Losses";
$lang["draw"]       = "Draw";

$lang["kills"]       = "Kills";
$lang["player_name"] = "Player name";
$lang["deaths"]      = "Deaths";
$lang["assists"]     = "Assists";
$lang["ck"]          = "Creep Kills";
$lang["cd"]          = "Creep Denies";
$lang["nk"]          = "Neutral Kills";

$lang["towers"]      = "Towers";
$lang["rax"]         = "Rax";
$lang["neutrals"]    = "Neutrals";
$lang["submit"]          = "Submit";

$lang["page"]          = "Page";
$lang["pageof"]        = "of";
$lang["total"]         = "total";
$lang["next_page"]     = "Next page";
$lang["previous_page"] = "Previous page";

$lang["fastest_game"]   = 'Fastest Game Won';
$lang["longest_game"]   = 'Longest Game Won';

$lang["game_history"]         = "Game History:";
$lang["user_game_history"]    = "User Game History";
$lang["best_player"]          = "Best Player: ";
$lang["show_hero_history"]    = "Show all games with this hero";

$lang["download_replay"]      = "Download replay";
$lang["view_gamelog"]         = "View Gamelog";

$lang["win_percent"]          = "Win %";
$lang["wl_percent"]           = "W/L%";
$lang["kd_ratio"]             = "K/D Ratio";
$lang["kd"]                   = "K/D";
$lang["kpg"]                  = "KPG";
$lang["kills_per_game"]       = "Kills per game";
$lang["dpg"]                  = "DPG";
$lang["apg"]                  = "APG";
$lang["assists_per_game"]     = "Assists per game";
$lang["ckpg"]                 = "CKPG";
$lang["creeps_per_game"]      = "Creep kills per game";
$lang["cdpg"]                 = "CDPG";
$lang["denies_per_game"]      = "Denies per game";
$lang["deaths_per_game"]      = "Deaths per game";
$lang["npg"]                  = "NPG";
$lang["neutrals_per_game"]    = "Neutrals per game";
$lang["search_results"]       = "Search results for: ";
$lang["user_not_found"]       = "User not found";
$lang["left_info"]            = "How many times a player has left the game before the end of the game";

$lang["admin"]       = "Admin";
$lang["server"]      = "Server";
$lang["voucher"]     = "Voucher";
$lang["role"]        = "Role";

$lang["banned"]     = "BANNED";
$lang["permanent_ban"]   = "Permanent";
$lang["reason"]     = "Reason";
$lang["game_name"]  = "Game Name";
$lang["bannedby"]   = "Banned by";
$lang["leaves"]     = "Leaves";
$lang["stayratio"]     = "Stay ratio";
$lang["leaver"]        = "Leaver";
$lang["streak"]        = "Streak";
$lang["longest_streak"]= "Winning Streak";
$lang["losing_streak"] = "Losing Streak";
$lang["zero_deaths"]   = "The total number of games where the player has 0 deaths";

$lang["comments"]             = "Comments";
$lang["latest_comments"]      = "Latest Comments";
$lang["add_comment"]          = "Add Comment";
$lang["add_comment_button"]   = "Add Comment";
$lang["reply"]                = "[reply]";

$lang["error_comment_not_allowed"]   = "Writing comments is not allowed for this post";
$lang["error_invalid_form"]          = "Invalid form";
$lang["error_text_char"]             = "Text does not have enough characters";

$lang["gamestate_priv"]       = "PRIV";
$lang["gamestate_pub"]        = "PUB";

//Login / Registration
$lang["login"]       = "Login";
$lang["logged_as"]   = "Logged as ";
$lang["email"]       = "E-mail";
$lang["avatar"]      = "Avatar";
$lang["location"]    = "Location";
$lang["realm"]       = "Realm";
$lang["website"]     = "Website";
$lang["gender"]      = "Gender";
$lang["male"]        = "Male";
$lang["female"]      = "Female";
$lang["password"]    = "Password";
$lang["register"]    = "Register";
$lang["username"]    = "Username";
$lang["language"]    = "Language";
$lang["confirm_password"]       = "Confirm Password";
$lang["change_password"]        = "Change Password";
$lang["change_password_info"]   = "Check this if you want to change the password";
$lang["comment_not_logged"]     = "You need to be logged in to post a comment.";
$lang["acc_activated"]          = "Account successfully activated. Now you can login.";
$lang["invalid_link"]           = "Link is not valid or expired.";

//Heroes and items
$lang["hero"]   = "Hero";
$lang["description"]     = "Description";
$lang["stats"]           = "Stats";
$lang["skills"]          = "Skills";

$lang["search"]          = "Search";
$lang["search_bans"]     = "Search bans...";
$lang["search_members"]  = "Search Members...";
$lang["search_heroes"]   = "Search heroes...";
$lang["search_items"]    = "Search items...";

//Errors
$lang["error_email"]      = "E-mail address is not valid";
$lang["error_email_banned"]      = "Email address is banned";
$lang["error_short_pw"]   = "Field Password does not have enough characters";
$lang["error_passwords"]  = "Password and confirmation password do not match";
$lang["error_inactive_acc"]   = "Account is not activated yet";
$lang["error_invalid_login"]  = "Invalid e-mail or password";
$lang["error_short_un"]   = "Field Username does not have enough characters";
$lang["error_un_taken"]   = "Username already taken";
$lang["error_username"]   = "Invalid username";
$lang["error_email_taken"]= "E-mail already taken";
$lang["error_email_send"] = "E-mail has already been sent";

//Email
$lang["email_charset"] = "UTF-8";
$lang["email_subject_activation"] = "Account Activation";
$lang["email_from"] = "no-reply@ohsystem.net";
$lang["email_from_full"] = "OpenHosting";
$lang["email_send"] = "Email message was sent successfully";

//Email text
$lang["email_activation1"] = "Hello";
$lang["email_activation2"] = "You have successfully registered to the site ";
$lang["email_activation3"] = "Click on the following link to confirm your email address and activate your account";

//GAME LOG
$lang["game_log"]            = "Game Log";
$lang["log_player"]          = "Player";
$lang["log_priv"]            = "[Priv]";
$lang["log_ally"]            = "[Allies]";
$lang["log_first_blood"]     = "for first blood";
$lang["log_suicide"]         = "has killed himself!";
$lang["log_denied_teammate"] = "denied his teammate";
$lang["log_level"]           = "level";
$lang["log_tower"]           = "tower";
$lang["log_barracks"]        = "barracks";

$lang["404_error"]           = "Oops, page not found";

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

$lang["upload_image"]      = "Upload image";
$lang["remove_avatar"]     = "Remove avatar";

//Gamelist patch
$lang["current_games"]      = "Current Games";
$lang["refresh"]            = "Refresh";
$lang["slots"]              = "Slots";
$lang["empty"]              = "Empty";
$lang["ms"]                 = "ms";
$lang["players"]            = "Players";

//Members
$lang["joined"]         = "Joined";
$lang["user_info"]      = "Info";

$lang["choose"]         = "Choose";
$lang["all_guides"]     = "---All guides---";

//Compare players
$lang["compare_back"]         = "&laquo; Back to Top players";
$lang["compare_list"]         = "List";
$lang["compare_list_empty"]   = "List is empty";
$lang["compare_compare"]      = "Compare";
$lang["compare_add"]          = "Add to compare list";
$lang["compare_clear"]        = "Clear list";
$lang["compare_remove_player"]   = "Remove player from list?";
$lang["compare_players"]      = "Player Comparison";
$lang["compare_empty_info"]   = "Su lista de jugadores para comparar está vacía";
$lang["overall"]              = "Total";
$lang["stay"]              = "Permanecer";

//Chat/GameLog
$lang["chat_attention"]     = "<b>Atención:</b> No abuse de esta función o jugadores spam, porque esto no está permitido y será castigado.";
$lang["chat_title_player"]  = "- enviar un mensaje directamente al jugador en el juego";
$lang["chat_successfull"]   = "<b>El mensaje se envió con éxito</b>";
$lang["chat_error1"]        = "No hay suficientes caracteres";

$lang["gl_lobby"]    = "[Lobby]";
$lang["gl_system"]   = "[System]";
$lang["gl_bot"]      = "[BOT]";
$lang["gl_picked"]   = "compró o recogió";
$lang["gl_swap"]     = "sugerió intercambiar con";
$lang["gl_mode"]     = "Modo se ha establecido a:";
$lang["gl_heropick"] = "Escogido un héroe";
$lang["gl_level"]    = "Ahora es de nivel";
$lang["gl_assists"]  = "Asistencias:";
$lang["gl_killed"]   = "asesinados";
$lang["gl_suicide"]  = "se suicidó.";
$lang["gl_used"]     = "usó ";
$lang["gl_stored"]   = "almacenado ";
$lang["gl_rune"]     = "runa.";
$lang["gl_killed_t"] = "mató a";
$lang["gl_tower"]    = "torre.";
$lang["gl_rax"]      = "rax.";
$lang["gl_attack"]   = "ataques";
$lang["gl_killed_by"]= "fue asesinado por el";
$lang["gl_item"]     = "compró";

$lang["gl_playerlist_sentinel_title"] = "Jugador (Level) - Sentinel";
$lang["gl_playerlist_scourge_title"] = "Jugador (Level) - Scourge";
$lang["gl_playerlist_kda"]        = "K/D/A";
$lang["gl_playerlist_send"]       = "Enviar un mensaje directamente al jugador en el juego";
$lang["gl_playerlist_sentinel"]       = "Sentinel";
$lang["gl_playerlist_scourge"]        = "Scourge";
$lang["gl_game_started"]              = "El juego ha iniciado";

$lang["gl_compare_sentinel"] = "Jugador - Sentinel";
$lang["gl_compare_scourge"]  = "Jugador - Scourge";
$lang["gl_compare_wl"]       = "W/L";
$lang["gl_compare_perc"]     = "%";
$lang["gl_compare_wl_sentinel"]       = "Ganados/Perdidos - SENTINEL";
$lang["gl_compare_sentinel_chance_t"]   = "Oportunidad de ganar - SENTINEL";
$lang["gl_compare_sentinel_chance"]     = "Sentinel tiene la oportunidad de ganar";

$lang["gl_compare_no"]     = "No hay suficientes jugadores.";

$lang["gl_compare_wl_scourge"]         = "Ganados/Perdidos - SCOURGE";
$lang["gl_compare_scourge_chance_t"]   = "Oportunidad de ganar - SCOURGE";
$lang["gl_compare_scourge_chance"]     = "Scourge tiene la oportunidad de ganar";

$lang["gl_calculate"]                  = "Calcular la oportunidad de ganar";
$lang["gl_not_ranked"]                 = "Sin calificación";
$lang["gl_no_accesss"]                 = "Usted no tiene acceso!";
$lang["gl_no_stats_data"]              = "No hay suficientes datos estadísticos";

$lang["gl_login"] = "Please <a href='".$website."?login'>login</a> to view all the action in the game";

$lang["member_comments"]     = "Comments";
$lang["member_registered"]   = "Registered";
$lang["member_location"]     = "Location";
$lang["member_realm"]        = "Realm";
$lang["member_website"]      = "Website";
$lang["member_gender"]       = "Género";
$lang["member_last_login"]   = "último acceso";
$lang["member_clan"]         = "Clan";


$lang["realm_username"]      = "Nombre de Usuario en el Realm";
$lang["protected_account"]   = "Cuenta protegida";
$lang["protect_account"]     = "Proteger la cuenta";
$lang["acount_is_protected"] = "Su cuenta está protegida en el Battle.net";
$lang["acount_is_not_protected"] = "Su cuenta no está protegida en el Battle.net";
$lang["acount_protected_step"] = "Un paso más para proteger su cuenta";
$lang["set_password"]        = "Establecer contraseña";
$lang["protect_info1"]       = "Usted puede proteger su cuenta con una contraseña.";
$lang["protect_info2"]       = "<b>!pw</b> <i>[password]</i> - verificar su cuenta en el juego / lobby";
$lang["protect_info3"]       = "<b>!pw</b> <i>[password] clear</i> - quitar la protección";
$lang["protect_info4"]       = "Más información";
$lang["protect_info5"]       = "Al establecer una contraseña, la cuenta no puede ser objeto de abuso por alguien más en Battle.net (utilice el mismo nombre que el suyo)";
$lang["protect_info6"]       = "Uso";
$lang["protect_info7"]       = "Si usted escribe la contraseña en el campo de arriba <b>mipassword</b>, puede utilizar el comando";
$lang["protect_info8"]       = "<b>!pw mipassword</b> para proteger su cuenta";
$lang["protect_info9"]       = "<b>!pw mipassword clear</b> - eliminar la protección o";
$lang["protect_info10"]      = "<b>!pw mipassword 0</b> - quitar la protección";
$lang["protect_info11"]      = "<b>Nota:</b> Si la contraseña está definida, y usted <b>no lo hizo</b> utilice el comando <b>!pw</b> o usted será expulsado a los 10 segundos";
$lang["protect_info12"]      = "<i>La contraseña no puede contener espacios en blanco</i>";

$lang["protect_info13"]      = "Ha introducido la contraseña, pero tu cuenta aún no está verificada en Battle.net";
$lang["protect_info14"]      = "Para verificar su cuenta en Battle.net, utilice el comando <b>!confirm email password</b>";
$lang["protect_info15"]      = "<b>Ejemplo:</b> Su email es <b>ejemplo@mail.com</b> y la contraseña es <b>password123</b>";
$lang["protect_info16"]      = "<b>Comando:</b> !confirm ejemplo@mail.com password123";
$lang["protect_info17"]      = "Cuenta confirmada tiene 2 iconos al lado del nombre de la cuenta: ";

$lang["protect_info18"]      = "<i>Esto no es una contraseña para su cuenta en el sitio web / Bnet, pero una contraseña para proteger su cuenta de Battle.net</i>";
$lang["protect_info19"]      = "<i>Esta contraseña se utiliza únicamente para nuestros bots de hosting</i>";
$lang["protect_info20"]      = "<i>Cuando se utiliza el comando<b>!pw</b>, otros usuarios no pueden ver lo que estás escribiendo</i>";
$lang["protect_info21"]      = "Muéstrame el comando";
$lang["protect_info22"]      = "Después de que su cuenta se verifica, puede eliminar la contraseña (si quieres).";

//OFFENCES
$lang["player_offences"]     = "Infracciones del jugador";
$lang["player_warned"]       = "Este jugador fue advertido";
$lang["has_total_of"]        = "Tiempos y tiene total de";
$lang["penalty_points"]      = "Puntos de penalización";
$lang["pp"]                  = "PP";

$lang["points"]              = "Puntos";

$lang["points_info1"]        = "Usted acaba de conseguir ";
$lang["points_info2"]        = "puntos";
$lang["points_info3"]        = "por iniciar sesión a diario";
$lang["points_info4"]        = "Nota: Usted debe ser clasificado con el fin de obtener estos puntos";

$lang["hide_stats"]          = "No volver a mostrar mis estadísticas";
$lang["show_stats"]          = "Mostrar mis estadísticas";
$lang["hide_stats_info"]     = "Si oculta las estadísticas, nadie será capaz de ver sus estadísticas (a excepción de los juegos individuales).<div>Debe confirmar su cuenta del battle para cambiar esta opción.</div>";
$lang["hide_stats_message"]  = "El usuario no desea mostrar sus estadísticas";
$lang["last_seen"]           = "Visto por última vez";
$lang["displlay_stats_for"]  = "Viendo las estadísticas de";
$lang["select_country"]      = "Seleccione el país";

$lang["most_recent_stats"]  = "Mostrar estadísticas más recientes";

$lang["game_log_lobby"]  = "Lobby Log";
$lang["game_log_game"]   = "Registro de Juego";
$lang["show_all"]        = "Mostrar todo";
$lang["choose_game_type"]        = "Elija el tipo de juego";
$lang["game_type"]               = "Tipo de juego";
$lang["live_games_disable"]      = "Usted está en el juego. Datos no disponibles.";
$lang["common_games"]      = "Juegos comunes";

$lang["top_teammates"]      = "Mejores compañeros de Equipo";
$lang["top_opponents"]      = "Mejores Oponentes";
?>
