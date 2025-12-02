#Requires: mysql-connector-python. Can be installed via pip if not already available.
# pip install mysql-connector-python in a console
import mysql.connector
import numpy as np
from typing import Tuple, List, Dict

# Define the expected return types
Matrix = np.ndarray
BusinessList = List[str]

# --- Database Configuration (UPDATE THESE VALUES) ---
DB_NAME = 'aiXX'
USER = 'aiXX'
PASSWORD = 'aiXX_password'
HOST = 'localhost'
PORT = 3306

def get_data() -> Tuple[Matrix, Matrix, BusinessList]:
    """
    Connects to the database to retrieve all business ratings and metadata,
    and converts them into NumPy matrices Y and R for matrix factorization/recommendation.

    Returns:
        Y: Matrix of ratings (scores). Dimensions: (num_businesses, num_users)
        R: Binary indicator matrix (1 if rated, 0 otherwise). Dimensions: (num_businesses, num_users)
        business_list: List of business names (strings) corresponding to the rows of Y and R.
    """
    
    # Dictionaries to store mappings from Int IDs to matrix indices
    business_id_to_index: Dict[int, int] = {}
    user_id_to_index: Dict[int, int] = {}
    
    # Lists to store final business names and ratings data
    business_list: BusinessList = []
    
    # --- 1. Database Connection ---
    try:
        conn = mysql.connector.connect(
            host=HOST,
            port=PORT,
            database=DB_NAME,
            user=USER,
            password=PASSWORD
        )
        # Use unbuffered cursor for potentially large datasets
        cursor = conn.cursor(buffered=True)

        # --- 2. Get Business List and Map Business IDs to Indices ---
        # NOTE: Assumes 'business' table has columns 'id' and 'name'
        cursor.execute("SELECT id, name FROM business ORDER BY id")
        
        # Populate business_list and the business_id_to_index map
        for i, (business_id, name) in enumerate(cursor):
            business_list.append(name)
            business_id_to_index[business_id] = i
        
        num_businesses = len(business_list)
        print(f"Loaded {num_businesses} businesses.")

        # --- 3. Get User Count and Map User IDs to Indices ---
        # We need the user IDs that actually appear in the ratings table.
        # This is more robust than COUNT(id) from the users table alone.
        
        # Get all unique user IDs from the 'review' table
        cursor.execute("SELECT DISTINCT user_id FROM review ORDER BY user_id")
        
        user_ids = [user_id for (user_id,) in cursor]
        
        # Populate user_id_to_index map
        for i, user_id in enumerate(user_ids):
            user_id_to_index[user_id] = i
            
        num_users = len(user_ids)
        print(f"Found {num_users} unique users in reviews.")
        
        # --- 4. Initialize Matrices ---
        # Python indices will be (business, user).
        Y = np.zeros((num_businesses, num_users))
        R = np.zeros((num_businesses, num_users))
        
        # --- 5. Build Matrices Y and R from Review Data ---
        # Assuming 'review' table has columns 'user_id', 'business_id', and 'stars' (the score)
        cursor.execute("SELECT user_id, business_id, stars FROM review")

        for user_id, business_id, score in cursor:
            try:
                # Convert the database IDs into matrix indices
                business_idx = business_id_to_index[business_id]
                user_idx = user_id_to_index[user_id]

                # Populate the matrices
                Y[business_idx, user_idx] = float(score)
                R[business_idx, user_idx] = 1

            except KeyError:
                # Should not happen if data integrity is maintained, but handles IDs not mapped.
                continue

        # --- 6. Cleanup ---
        cursor.close()
        conn.close()

        # --- 7. Return Results ---
        return Y, R, business_list

    except mysql.connector.Error as err:
        print(f"Database error: {err}")
        # Return empty matrices on failure
        return np.array([]), np.array([]), []


# --- Example of running the function ---
if __name__ == '__main__':
    # NOTE: You must have your MariaDB/MySQL server running and the database 'aiXX' 
    # populated with the 'business', 'user', and 'review' tables with INT IDs 
    # for this to work correctly.
    
    # Y_matrix, R_matrix, b_list = get_data()

    # if Y_matrix.size > 0:
    #     print("\nData Loading Successful.")
    #     print(f"Y matrix shape (Businesses x Users): {Y_matrix.shape}")
    #     print(f"R matrix shape: {R_matrix.shape}")
    #     print(f"First 5 Businesses: {b_list[:5]}")
    #     # Example of a rating:
    #     # print(f"First business's ratings: {Y_matrix[0, :]}")
    # else:
    #     print("\nData Loading Failed. Check database configuration and connection.")
