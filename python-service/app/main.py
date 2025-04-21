from fastapi import FastAPI, Request, Header
from pydantic import BaseModel
from app.transformer import transform_file
import os
import httpx

app = FastAPI()

class TransformRequest(BaseModel):
    file_id: int
    file_path: str

@app.post("/api/transform")
async def transform(data: TransformRequest, authorization: str = Header(None)):
    print(authorization,os.getenv('API_TOKEN'))
    if authorization != f"Bearer {os.getenv('API_TOKEN')}":
        return {"message": "Unauthorized"}, 401

    result_file, status = transform_file(data.file_path, data.file_id)
    print(result_file, status)
    async with httpx.AsyncClient() as client:
            response = await client.post(
            os.getenv("PHP_UPDATE_URL"),
            headers={"Authorization": f"Bearer {os.getenv('API_TOKEN')}"},
            json={
                    "file_id": data.file_id,
                    "status": status,
                    "result_path": result_file,
                }
            )
            print(f"Response status code: {response.status_code}")
            print(f"Response content: {response.text}")

    return {"message": "Transformation complete", "result": result_file}
