
/*zmienia wszystko oprocz loginu i hasła*/
UPDATE table Konto
SET plec =  ,
waga= ,
wzrost= ,
zgoda_ranking =
WHERE login = '' AND haslo = '';
/*end*/

UPDATE table Konto
SET haslo =  ''
WHERE login = '' AND haslo = '';

UPDATE table Konto
SET plec =  /*num (0 lub 1)*/
WHERE login = '' AND haslo = '';

UPDATE table Konto
SET  waga =
WHERE login = '' AND haslo = '';

UPDATE table Konto
SET wzrost =
WHERE login = '' AND haslo = '';

UPDATE table Konto
SET zgoda_ranking =  /*num (0 lub 1)*/
WHERE login = '' AND haslo = '';


/*znajomi - konwersja do dwukierunkowych*/
SELECT * from ZNAJOMI
UNION
SELECT ZNAJOMY2,ZNAJOMY1 from ZNAJOMI;

SELECT K.LOGIN from KONTO K LEFt JOIN
                  (
                      SELECT *
                      from KONTO
                               LEFT JOIN
                           (SELECT *
                            from ZNAJOMI
                            UNION
                            SELECT ZNAJOMY2, ZNAJOMY1
                            from ZNAJOMI) P
                           ON KONTO.id = P.ZNAJOMY1
                      WHERE KONTO.LOGIN = 'janusz'
                  ) T
ON K.ID = T.ZNAJOMY2 WHERE T.LOGIN is not null;


SELECT LOGIN,NAZWA,SUM(ILOSC) odleglosc,SUM(CZAS_TRWANIA)czas
from (
SELECT LOGIN,T.NAZWA,ILOSC,CZAS_TRWANIA
    from KONTO K
    INNER JOIN AKTYWNOSC A on K.ID = A.ID
    INNER JOIN TYP_AKTYWNOSCI T on t.ID = A.ID_RODZAJU
    INNER JOIN UCZESTNICY_WYZWANIA UW on UW.UCZESTNIK = K.ID
    INNER JOIN WYZWANIE W on UW.WYZWANIE = W.ID AND A.ID_RODZAJU = W.ID_AKTYWNOSCI
    WHERE W.ID = ?
    AND A.DATA_ROZPOCZECIA <= W.CZAS_UKONCZENIA AND A.DATA_ROZPOCZECIA>= W.CZAS_ROZPOCZECIA
) GROUP BY LOGIN, NAZWA;

SELECT T.ID, SUM(A.ILOSC) from TYP_AKTYWNOSCI T
    INNER JOIN AKTYWNOSC A on T.ID = A.ID_RODZAJU AND A.ID = ? GROUP BY T.ID;


SELECT  UW.WYZWANIE from UCZESTNICY_WYZWANIA UW
        INNER JOIN KONTO K on K.ID = UW.UCZESTNIK AND K.ID = ?
        INNER JOIN WYZWANIE W on UW.WYZWANIE = W.ID ORDER BY W.CZAS_UKONCZENIA DESC;



/* i sent them friend invitation */
    SELECT ZAPROSZONY from ZAPROSZENIA_DO_ZNAJOMYCH WHERE ZAPRASZAJACY = $id;

/* they sent me a friend invitation */
    SELECT ZAPRASZAJACY from ZAPROSZENIA_DO_ZNAJOMYCH WHERE ZAPROSZONY = $id;

/* my friends */
SELECT ZNAJOMY2 from ZNAJOMI WHERE ZNAJOMI.ZNAJOMY1 = $id
UNION
SELECT ZNAJOMY1 from ZNAJOMI WHERE ZNAJOMI.ZNAJOMY2 = $id;


/* unknown without sent friend invitation in progress */
SELECT ID from KONTO WHERE ID <> $id
MINUS
(
    SELECT ZNAJOMY2 from ZNAJOMI WHERE ZNAJOMI.ZNAJOMY1 = $id
    UNION
    SELECT ZNAJOMY1 from ZNAJOMI WHERE ZNAJOMI.ZNAJOMY2 = $id
    UNION
    SELECT ZAPRASZAJACY from ZAPROSZENIA_DO_ZNAJOMYCH WHERE ZAPROSZONY = $id
    UNION
    SELECT ZAPROSZONY from ZAPROSZENIA_DO_ZNAJOMYCH WHERE ZAPRASZAJACY = $id
);

UPDATE AKTYWNOSC
    SET kcal = (SELECT waga
                 FROM KONTO
                 WHERE KONTO.id = AKTYWNOSC.ID)
    WHERE ID_RODZAJU=1;
UPDATE AKTYWNOSC
    SET kcal = CZAS_TRWANIA * 6 * 3.5 * kcal /200
    WHERE ID_RODZAJU=1;

commit;

UPDATE AKTYWNOSC
    SET kcal = (SELECT waga
                 FROM KONTO
                 WHERE KONTO.id = AKTYWNOSC.ID)
    WHERE ID_RODZAJU=2;
UPDATE AKTYWNOSC
    SET kcal = CZAS_TRWANIA * 3.8 * 3.5 * kcal /200
    WHERE ID_RODZAJU=2;
commit;

UPDATE AKTYWNOSC
    SET kcal = (SELECT waga
                 FROM KONTO
                 WHERE KONTO.id = AKTYWNOSC.ID)
    WHERE ID_RODZAJU=3;
UPDATE AKTYWNOSC
    SET kcal = CZAS_TRWANIA * 8 * 3.5 * kcal /200
    WHERE ID_RODZAJU=3;
commit;

