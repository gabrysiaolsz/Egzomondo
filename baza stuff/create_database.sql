DROP TABLE Wyzwania;
DROP TABLE Aktywnosc;
DROP TABLE Znajomi;
DROP TABLE  Konto;
DROP TABLE Typ_aktywnosci;


CREATE TABLE Konto
(
    id    number primary key,
    login VARCHAR2(20)  unique not null ,
    haslo VARCHAR2(20) not null ,
    plec NUMBER ,
    waga NUMBER,
    wzrost NUMBER,
    zgoda_ranking NUMBER
);

CREATE TABLE Znajomi
(
    znajomy1 REFERENCES Konto,
    znajomy2 REFERENCES Konto,
    CONSTRAINT rozni CHECK ( znajomy1 <> znajomy2 )
);

CREATE TABLE Typ_aktywnosci
(
    id number primary key,
    nazwa varchar2(20)
);


CREATE TABLE Wyzwania
(
    id number primary key,
    nazwa VARCHAR2(20) not null,
    czas_rozpoczecia DATE not null,
    czas_ukonczenia DATE,
    postep NUMBER not null ,
    cel NUMBER not null ,
    jednostka_celu VARCHAR2(20),
    czy_prywatne NUMBER DEFAULT 0,
    id_aktywnosci NUMBER REFERENCES Typ_aktywnosci
);

CREATE TABLE Aktywnosc
(
    id number REFERENCES Konto,
    id_rodzaju number REFERENCES Typ_aktywnosci,
    ilosc number not null,
    data_rozpoczecia DATE not null ,
    czas_trwania number not null
);

commit ;