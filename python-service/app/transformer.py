import os, json, pandas as pd
from app.logger import log
from pathlib import Path

def transform_file(path, file_id):
    print(f"Processing file {file_id} at path {path}")
    try:
        ext = Path(path).suffix
        result = {}
        if ext in ['.csv', '.xlsx']:
            df = pd.read_csv(path) if ext == '.csv' else pd.read_excel(path)
            result['columns'] = df.dtypes.astype(str).to_dict()
            result['null_percentage'] = df.isnull().mean().round(2).to_dict()
            result['duplicates'] = int(df.duplicated().sum())
            result['top_5_rows'] = df.head().to_dict(orient='records')
        elif ext == '.json':
            with open(path) as f:
                data = json.load(f)
            result['keys'] = list(data.keys()) if isinstance(data, dict) else []
            result['depth'] = get_depth(data)
            result['structure'] = map_structure(data)
        else:
            return None, 'unsupported'

        result_path = f"{os.getenv('RESULTS_PATH')}/result_{file_id}.json"
        with open(result_path, 'w') as f:
            json.dump(result, f, indent=2)

        log(f"File {file_id} processed successfully.")
        return result_path, 'completed'
    except Exception as e:
        log(f"Error processing file {file_id}: {str(e)}")
        return None, 'error'

def get_depth(obj, level=1):
    if isinstance(obj, dict):
        return max([get_depth(v, level + 1) for v in obj.values()] + [level])
    elif isinstance(obj, list):
        return max([get_depth(i, level + 1) for i in obj] + [level])
    else:
        return level

def map_structure(data):
    if isinstance(data, dict):
        return {k: map_structure(v) for k, v in data.items()}
    elif isinstance(data, list):
        return [map_structure(data[0])] if data else []
    else:
        return type(data).__name__
