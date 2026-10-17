/* =====================================================================
   GESTIÓN FINANCIERA, CLIENTES Y FUERZA DE VENTAS — consultas revisadas
   Dialecto: SQL Server (T-SQL). También corren en MySQL.
   Cambios marcados con  -- [CORREGIDO]  o  -- [MEJORA]
   ===================================================================== */

-- 1. Listado general de clientes y sus adopciones  (sin errores)
SELECT
    person.id                AS client_id,
    person.name              AS client_name,
    person.email             AS client_email,
    adoption.id              AS adoption_id,
    adoption.requested_at    AS request_date,
    adoption.approved_at     AS approval_date,
    adoption.delivered_at    AS delivery_date,
    adoption_status.name     AS adoption_status
FROM people AS person
LEFT JOIN adoptions AS adoption
    ON adoption.person_id = person.id
LEFT JOIN adoption_statuses AS adoption_status
    ON adoption_status.id = adoption.adoption_status_id
ORDER BY
    person.name,
    adoption.requested_at DESC;


-- 2. Clientes que todavía no han realizado una adopción  (sin errores)
SELECT
    person.id      AS client_id,
    person.name    AS client_name,
    person.email   AS client_email,
    person.income  AS monthly_income
FROM people AS person
LEFT JOIN adoptions AS adoption
    ON adoption.person_id = person.id
WHERE adoption.id IS NULL
ORDER BY
    person.name;


-- 3. Resumen de adopciones por cliente  (sin errores)
SELECT
    person.id    AS client_id,
    person.name  AS client_name,
    COUNT(adoption.id) AS total_adoptions,
    SUM(CASE WHEN adoption.delivered_at IS NOT NULL THEN 1 ELSE 0 END) AS completed_adoptions,
    MAX(adoption.requested_at) AS last_request_date
FROM people AS person
LEFT JOIN adoptions AS adoption
    ON adoption.person_id = person.id
GROUP BY
    person.id,
    person.name
ORDER BY
    completed_adoptions DESC,
    total_adoptions DESC;


-- 4. Clientes con mayor capacidad económica  (sin errores)
SELECT
    person.id        AS client_id,
    person.name      AS client_name,
    person.email     AS client_email,
    person.income    AS monthly_income,
    occupation.name  AS occupation,
    residence.name   AS residence
FROM people AS person
INNER JOIN occupations AS occupation
    ON occupation.id = person.occupation_id
INNER JOIN residences AS residence
    ON residence.id = person.residence_id
WHERE person.income >= 10000
ORDER BY
    person.income DESC;


-- 5. Rendimiento de la fuerza de ventas por usuario
-- [CORREGIDO] Se agregan adopciones y contratos por separado antes de unirlos.
--             Unir ambas tablas directamente a users multiplicaba las filas
--             (adopciones x contratos) e inflaba TODAS las métricas.
SELECT
    sales_user.id    AS sales_user_id,
    sales_user.name  AS sales_user_name,
    COALESCE(adoption_stats.total_requests, 0)       AS total_requests,
    COALESCE(adoption_stats.approved_requests, 0)    AS approved_requests,
    COALESCE(adoption_stats.completed_adoptions, 0)  AS completed_adoptions,
    COALESCE(contract_stats.generated_contracts, 0)  AS generated_contracts
FROM users AS sales_user
LEFT JOIN (
    SELECT
        adoption.user_id,
        COUNT(adoption.id) AS total_requests,
        SUM(CASE WHEN adoption.approved_at  IS NOT NULL THEN 1 ELSE 0 END) AS approved_requests,
        SUM(CASE WHEN adoption.delivered_at IS NOT NULL THEN 1 ELSE 0 END) AS completed_adoptions
    FROM adoptions AS adoption
    GROUP BY adoption.user_id
) AS adoption_stats
    ON adoption_stats.user_id = sales_user.id
LEFT JOIN (
    SELECT
        contract.user_id,
        COUNT(contract.id) AS generated_contracts
    FROM contracts AS contract
    GROUP BY contract.user_id
) AS contract_stats
    ON contract_stats.user_id = sales_user.id
ORDER BY
    completed_adoptions DESC,
    approved_requests DESC;


-- 6. Tasa de conversión por integrante de ventas  (sin errores)
SELECT
    sales_user.id    AS sales_user_id,
    sales_user.name  AS sales_user_name,
    COUNT(adoption.id) AS total_requests,
    SUM(CASE WHEN adoption.delivered_at IS NOT NULL THEN 1 ELSE 0 END) AS completed_adoptions,
    CAST(
        100.0 * SUM(CASE WHEN adoption.delivered_at IS NOT NULL THEN 1 ELSE 0 END)
        / NULLIF(COUNT(adoption.id), 0)
        AS DECIMAL(5, 2)
    ) AS completion_rate_percentage
FROM users AS sales_user
LEFT JOIN adoptions AS adoption
    ON adoption.user_id = sales_user.id
GROUP BY
    sales_user.id,
    sales_user.name
ORDER BY
    completion_rate_percentage DESC,
    total_requests DESC;            -- [MEJORA] desempate por volumen


-- 7. Adopciones gestionadas por mes y por usuario
-- [CORREGIDO] Se agrupa también por sales_user.id; agrupar solo por el nombre
--             fusiona a dos usuarios que se llamen igual.
SELECT
    YEAR(adoption.requested_at)   AS request_year,
    MONTH(adoption.requested_at)  AS request_month,
    sales_user.id                 AS sales_user_id,
    sales_user.name               AS sales_user_name,
    COUNT(adoption.id)            AS total_requests,
    SUM(CASE WHEN adoption.approved_at IS NOT NULL THEN 1 ELSE 0 END) AS approved_requests
FROM adoptions AS adoption
INNER JOIN users AS sales_user
    ON sales_user.id = adoption.user_id
GROUP BY
    YEAR(adoption.requested_at),
    MONTH(adoption.requested_at),
    sales_user.id,
    sales_user.name
ORDER BY
    request_year,
    request_month,
    total_requests DESC;


-- 8. Contratos generados por la fuerza de ventas  (sin errores)
SELECT
    sales_user.id    AS sales_user_id,
    sales_user.name  AS sales_user_name,
    COUNT(contract.id)        AS total_contracts,
    MIN(contract.created_at)  AS first_contract_date,
    MAX(contract.created_at)  AS last_contract_date
FROM users AS sales_user
LEFT JOIN contracts AS contract
    ON contract.user_id = sales_user.id
GROUP BY
    sales_user.id,
    sales_user.name
ORDER BY
    total_contracts DESC;


-- 9. Resumen financiero de donaciones  (sin errores)
SELECT
    donation.contribution_type,
    COUNT(donation.id)    AS contribution_count,
    SUM(donation.amount)  AS total_amount,
    CAST(AVG(donation.amount) AS DECIMAL(10, 2)) AS average_amount,   -- [MEJORA] 2 decimales
    MIN(donation.amount)  AS minimum_amount,
    MAX(donation.amount)  AS maximum_amount
FROM donations AS donation
GROUP BY
    donation.contribution_type
ORDER BY
    total_amount DESC;


-- 10. Donaciones recibidas por mes  (sin errores)
--     Nota: donations no tiene columna de fecha propia; created_at es la fecha de registro.
SELECT
    YEAR(donation.created_at)   AS donation_year,
    MONTH(donation.created_at)  AS donation_month,
    COUNT(donation.id)          AS donation_count,
    SUM(donation.amount)        AS total_amount
FROM donations AS donation
GROUP BY
    YEAR(donation.created_at),
    MONTH(donation.created_at)
ORDER BY
    donation_year,
    donation_month;


-- 11. Ranking de donantes  (sin errores)
SELECT
    donor.id      AS donor_id,
    donor.name    AS donor_name,
    person.email  AS donor_email,
    COUNT(donation.id)    AS total_contributions,
    SUM(donation.amount)  AS total_donated
FROM donors AS donor
INNER JOIN people AS person
    ON person.id = donor.person_id
INNER JOIN donations AS donation
    ON donation.donor_id = donor.id
GROUP BY
    donor.id,
    donor.name,
    person.email
ORDER BY
    total_donated DESC;


-- 12. Donaciones asociadas a mascotas  (sin errores)
SELECT
    pet.id    AS pet_id,
    pet.name  AS pet_name,
    COUNT(donation.id)    AS contribution_count,
    SUM(donation.amount)  AS total_amount
FROM pets AS pet
INNER JOIN donations AS donation
    ON donation.pet_id = pet.id
GROUP BY
    pet.id,
    pet.name
ORDER BY
    total_amount DESC;


-- 13. Clientes que también son donantes
-- [CORREGIDO] La versión original devolvía a TODO donante (todo donor tiene person_id),
--             no solo a los que además son clientes. Se exige que tengan al menos una adopción.
SELECT
    person.id     AS client_id,
    person.name   AS client_name,
    person.email  AS client_email,
    COUNT(donation.id)    AS donation_count,
    SUM(donation.amount)  AS total_donated
FROM people AS person
INNER JOIN donors AS donor
    ON donor.person_id = person.id
INNER JOIN donations AS donation
    ON donation.donor_id = donor.id
WHERE EXISTS (
    SELECT 1
    FROM adoptions AS adoption
    WHERE adoption.person_id = person.id
)
GROUP BY
    person.id,
    person.name,
    person.email
ORDER BY
    total_donated DESC;


-- 14. Costos de tratamientos aplicados a mascotas
-- [MEJORA] Se cuenta la PK de pet_treatments y se agrupa también por id del tipo.
SELECT
    treatment_type.name   AS treatment_type,
    COUNT(pet_treatment.id)  AS treatments_applied,
    SUM(treatment.cost)      AS total_treatment_cost,
    CAST(AVG(treatment.cost) AS DECIMAL(10, 2)) AS average_treatment_cost
FROM pet_treatments AS pet_treatment
INNER JOIN treatments AS treatment
    ON treatment.id = pet_treatment.treatment_id
INNER JOIN treatment_types AS treatment_type
    ON treatment_type.id = treatment.treatment_type_id
GROUP BY
    treatment_type.id,
    treatment_type.name
ORDER BY
    total_treatment_cost DESC;


-- 15. Comparación entre donaciones y costos de tratamientos
-- [CORREGIDO] COALESCE: si una de las tablas está vacía, SUM devuelve NULL
--             y el balance completo salía NULL.
-- [CORREGIDO] En SQL Server, la instrucción anterior a WITH debe terminar en ';'
;WITH financial_summary AS
(
    SELECT
        COALESCE(SUM(donation.amount), 0) AS total_donations
    FROM donations AS donation
),
treatment_summary AS
(
    SELECT
        COALESCE(SUM(treatment.cost), 0) AS total_treatment_costs
    FROM pet_treatments AS pet_treatment
    INNER JOIN treatments AS treatment
        ON treatment.id = pet_treatment.treatment_id
)
SELECT
    financial_summary.total_donations,
    treatment_summary.total_treatment_costs,
    financial_summary.total_donations
        - treatment_summary.total_treatment_costs AS financial_balance
FROM financial_summary
CROSS JOIN treatment_summary;
