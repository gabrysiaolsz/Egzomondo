
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
