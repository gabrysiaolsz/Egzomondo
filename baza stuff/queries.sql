CREATE TABLE Konto
(
    id    number primary key,
    login VARCHAR2(20)  unique not null ,
    haslo VARCHAR2(20) not null ,
    plec NUMBER ,
    waga NUMBER,
    wzrost NUMBER,
    zgoda_ranking NUMBER


UPDATE table Konto
SET haslo =  ''
WHERE login = '';

UPDATE table Konto
SET plec =  /*num (0 lub 1)*/
WHERE login = '';

UPDATE table Konto
SET  waga =
WHERE login = '';

UPDATE table Konto
SET wzrost =
WHERE login = '';

UPDATE table Konto
SET zgoda_ranking =  /*num (0 lub 1)*/
WHERE login = '';